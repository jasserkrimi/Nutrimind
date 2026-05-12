<?php

class AIEmailGenerator {
    private $apiKey;
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.3-70b-versatile'; // Updated to latest model

    public function __construct($apiKey = null) {
        // Load API key from parameter, environment variable, or config file
        if ($apiKey) {
            $this->apiKey = $apiKey;
        } else {
            // Try environment variable first
            $this->apiKey = getenv('GROQ_API_KEY');
            
            // If not found, try loading from .env file
            if (empty($this->apiKey)) {
                $this->loadEnvFile();
                $this->apiKey = getenv('GROQ_API_KEY');
            }
            
            // If still not found, check for config file
            if (empty($this->apiKey)) {
                $configFile = __DIR__ . '/groq_config.php';
                if (file_exists($configFile)) {
                    $config = require $configFile;
                    $this->apiKey = $config['api_key'] ?? '';
                }
            }
        }
        
        if (empty($this->apiKey)) {
            throw new Exception('GROQ_API_KEY not configured. Please set it in environment variable, .env file, or groq_config.php');
        }
    }
    
    /**
     * Load environment variables from .env file
     */
    private function loadEnvFile() {
        $envFile = __DIR__ . '/.env';
        if (!file_exists($envFile)) {
            return;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Generate personalized email using AI
     */
    public function generateEmail($userData, $templateType, $tone = 'friendly') {
        // Calculate additional user metrics
        $metrics = $this->calculateUserMetrics($userData);
        
        // Build AI prompt based on template type
        $prompt = $this->buildPrompt($userData, $metrics, $templateType, $tone);
        
        // Call Groq API
        $response = $this->callGroqAPI($prompt);
        
        return $response;
    }

    /**
     * Calculate user metrics (BMI, engagement, etc.)
     */
    private function calculateUserMetrics($userData) {
        $metrics = [
            'bmi' => null,
            'bmi_category' => 'Unknown',
            'profile_completion' => 0,
            'days_since_registration' => 0,
            'days_since_last_login' => null,
            'engagement_status' => 'Unknown',
            'missing_fields' => [],
            'has_allergies' => false
        ];

        // Calculate BMI
        if (!empty($userData['poids']) && !empty($userData['taille'])) {
            $heightInMeters = $userData['taille'] / 100;
            $metrics['bmi'] = round($userData['poids'] / ($heightInMeters * $heightInMeters), 1);
            
            if ($metrics['bmi'] < 18.5) {
                $metrics['bmi_category'] = 'Underweight';
            } elseif ($metrics['bmi'] < 25) {
                $metrics['bmi_category'] = 'Healthy';
            } elseif ($metrics['bmi'] < 30) {
                $metrics['bmi_category'] = 'Overweight';
            } else {
                $metrics['bmi_category'] = 'Obese';
            }
        }

        // Profile completion
        $totalFields = 7; // nom, email, age, poids, taille, allergique, role
        $filledFields = 2; // nom and email are always filled
        
        if (!empty($userData['age'])) $filledFields++;
        if (!empty($userData['poids'])) $filledFields++;
        if (!empty($userData['taille'])) $filledFields++;
        if (isset($userData['allergique'])) $filledFields++;
        if (!empty($userData['role'])) $filledFields++;
        
        $metrics['profile_completion'] = round(($filledFields / $totalFields) * 100);

        // Missing fields
        if (empty($userData['age'])) $metrics['missing_fields'][] = 'âge';
        if (empty($userData['poids'])) $metrics['missing_fields'][] = 'poids';
        if (empty($userData['taille'])) $metrics['missing_fields'][] = 'taille';

        // Days since registration
        if (!empty($userData['date_creation'])) {
            $registrationDate = new DateTime($userData['date_creation']);
            $now = new DateTime();
            $metrics['days_since_registration'] = $now->diff($registrationDate)->days;
        }

        // Days since last login
        if (!empty($userData['last_login'])) {
            $lastLoginDate = new DateTime($userData['last_login']);
            $now = new DateTime();
            $metrics['days_since_last_login'] = $now->diff($lastLoginDate)->days;
            
            // Engagement status
            if ($metrics['days_since_last_login'] <= 7) {
                $metrics['engagement_status'] = 'Super Active';
            } elseif ($metrics['days_since_last_login'] <= 14) {
                $metrics['engagement_status'] = 'Active';
            } elseif ($metrics['days_since_last_login'] <= 30) {
                $metrics['engagement_status'] = 'Dormant';
            } else {
                $metrics['engagement_status'] = 'At Risk';
            }
        } else {
            $metrics['engagement_status'] = 'Never Logged In';
        }

        // Allergies
        $metrics['has_allergies'] = !empty($userData['allergique']) && $userData['allergique'] == 1;

        return $metrics;
    }

    /**
     * Build AI prompt based on template type
     */
    private function buildPrompt($userData, $metrics, $templateType, $tone) {
        $baseContext = "You are a professional email copywriter for Nutrimind, a nutrition and health management platform. ";
        $baseContext .= "Write in French. Be concise, engaging, and personalized. ";
        $baseContext .= "Tone: {$tone}. ";
        
        $userContext = "\n\nUser Information:\n";
        $userContext .= "- Name: {$userData['nom']}\n";
        $userContext .= "- Email: {$userData['email']}\n";
        $userContext .= "- Role: {$userData['role']}\n";
        $userContext .= "- Profile Completion: {$metrics['profile_completion']}%\n";
        $userContext .= "- Engagement Status: {$metrics['engagement_status']}\n";
        
        if ($metrics['bmi']) {
            $userContext .= "- BMI: {$metrics['bmi']} ({$metrics['bmi_category']})\n";
        }
        
        if ($metrics['days_since_last_login'] !== null) {
            $userContext .= "- Days Since Last Login: {$metrics['days_since_last_login']}\n";
        }
        
        if (!empty($metrics['missing_fields'])) {
            $userContext .= "- Missing Profile Fields: " . implode(', ', $metrics['missing_fields']) . "\n";
        }
        
        if ($metrics['has_allergies']) {
            $userContext .= "- Has Food Allergies: Yes\n";
        }

        // Template-specific prompts
        $templatePrompts = [
            'welcome' => "Write a warm welcome email for a new user. Encourage them to complete their profile and explore the platform. Include a clear call-to-action.",
            
            'reengagement' => "Write a re-engagement email for an inactive user. Make them feel missed, remind them of the platform's benefits, and give them a compelling reason to return. Be empathetic about their absence.",
            
            'profile_completion' => "Write an email encouraging the user to complete their profile. Explain why completing their profile (age, weight, height) is important for personalized nutrition recommendations. Make it quick and easy.",
            
            'health_milestone' => "Write a congratulatory email celebrating the user's health progress. Be motivational and encouraging. Suggest next steps to maintain their progress.",
            
            'allergy_alert' => "Write a safety-focused email for a user with food allergies. Remind them to keep their allergy information updated and check meal plans carefully. Be caring and protective.",
            
            'birthday' => "Write a birthday email for the user. Make it personal, warm, and celebratory. Offer a special birthday message related to health and wellness.",
            
            'progress_report' => "Write an email summarizing the user's progress and activity. Be data-driven but encouraging. Highlight achievements and suggest areas for improvement.",
            
            'tips' => "Write an email with personalized health and nutrition tips based on the user's profile. Be educational, practical, and actionable."
        ];

        $templateInstruction = $templatePrompts[$templateType] ?? $templatePrompts['tips'];

        $formatInstruction = "\n\nFormat Requirements:\n";
        $formatInstruction .= "- Return ONLY a JSON object with two fields: 'subject' and 'body'\n";
        $formatInstruction .= "- Subject: A catchy email subject line (max 60 characters) with 1-2 relevant emojis\n";
        $formatInstruction .= "- Body: The email content in HTML format with proper formatting (paragraphs, line breaks, bold text where appropriate)\n";
        $formatInstruction .= "- Use the user's first name naturally in the email\n";
        $formatInstruction .= "- Include a clear call-to-action button text in the email\n";
        $formatInstruction .= "- Keep the email concise (200-300 words)\n";
        $formatInstruction .= "- End with a warm signature from 'L'équipe Nutrimind 💚'\n";
        $formatInstruction .= "- Example JSON format: {\"subject\": \"🎉 Subject here\", \"body\": \"<p>Email content here...</p>\"}\n";

        return $baseContext . $userContext . "\n\n" . $templateInstruction . $formatInstruction;
    }

    /**
     * Call Groq API
     */
    private function callGroqAPI($prompt) {
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add this for Windows/XAMPP

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($curlError) {
            return [
                'success' => false,
                'error' => 'Connection error: ' . $curlError
            ];
        }

        // Decode response to get error details
        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMessage = 'API request failed with code ' . $httpCode;
            
            // Try to get more specific error from API response
            if (isset($result['error']['message'])) {
                $errorMessage .= ': ' . $result['error']['message'];
            } elseif (isset($result['error'])) {
                $errorMessage .= ': ' . (is_string($result['error']) ? $result['error'] : json_encode($result['error']));
            }
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'response' => $response,
                'http_code' => $httpCode
            ];
        }
        
        if (isset($result['choices'][0]['message']['content'])) {
            $content = $result['choices'][0]['message']['content'];
            
            // Try to extract JSON from the response
            $jsonMatch = [];
            if (preg_match('/\{[^}]*"subject"[^}]*"body"[^}]*\}/s', $content, $jsonMatch)) {
                $emailData = json_decode($jsonMatch[0], true);
                if ($emailData) {
                    return [
                        'success' => true,
                        'subject' => $emailData['subject'],
                        'body' => $emailData['body']
                    ];
                }
            }
            
            // Fallback: try to parse the entire content as JSON
            $emailData = json_decode($content, true);
            if ($emailData && isset($emailData['subject']) && isset($emailData['body'])) {
                return [
                    'success' => true,
                    'subject' => $emailData['subject'],
                    'body' => $emailData['body']
                ];
            }
            
            // If JSON parsing fails, return raw content
            return [
                'success' => false,
                'error' => 'Could not parse AI response',
                'raw_content' => $content
            ];
        }

        return [
            'success' => false,
            'error' => 'Invalid API response - no content received',
            'response' => $result
        ];
    }

    /**
     * Generate bulk emails for multiple users
     */
    public function generateBulkEmails($users, $templateType, $tone = 'friendly') {
        $results = [];
        
        foreach ($users as $user) {
            $result = $this->generateEmail($user, $templateType, $tone);
            $results[] = [
                'user_id' => $user['id'],
                'user_name' => $user['nom'],
                'user_email' => $user['email'],
                'email_data' => $result
            ];
            
            // Small delay to respect API rate limits (30 req/min = 2 seconds between requests)
            usleep(100000); // 0.1 second delay
        }
        
        return $results;
    }
}
?>
