<?php
/**
 * AI-Powered Profile Dashboard
 * Uses Groq AI to provide intelligent insights and recommendations
 */

class ProfileDashboardAI {
    private $apiKey;
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.3-70b-versatile';

    public function __construct() {
        // Load API key from environment variable or config file
        $this->apiKey = getenv('GROQ_API_KEY') ?: '';
        if (empty($this->apiKey)) {
            throw new Exception('GROQ_API_KEY environment variable is not set');
        }
    }

    /**
     * Generate comprehensive user insights
     */
    public function generateUserInsights($userData, $activityStats, $engagementScore) {
        $bmi = $this->calculateBMI($userData);
        $profileCompletion = $this->calculateProfileCompletion($userData);
        
        $prompt = "You are an expert user behavior analyst for a nutrition and health platform called Nutrimind.

Analyze this user profile and provide actionable insights in French:

USER DATA:
- Name: {$userData['nom']}
- Email: {$userData['email']}
- Role: {$userData['role']}
- Age: " . ($userData['age'] ?? 'Non renseigné') . "
- Weight: " . ($userData['poids'] ?? 'Non renseigné') . " kg
- Height: " . ($userData['taille'] ?? 'Non renseigné') . " cm
- BMI: " . ($bmi ?? 'Non calculé') . "
- Allergies: " . ($userData['allergique'] == 1 ? 'Oui' : 'Non') . "
- Registration Date: {$userData['date_creation']}
- Last Login: " . ($userData['last_login'] ?? 'Jamais') . "
- Status: {$userData['status']}

ENGAGEMENT METRICS:
- Engagement Score: {$engagementScore}%
- Profile Completion: {$profileCompletion}%
- Total Activities: {$activityStats['total_activities']}
- Recent Activities (7 days): {$activityStats['recent_activities']}
- Most Common Activity: {$activityStats['most_common_activity']}

Provide a comprehensive analysis in the following JSON format:
{
  \"summary\": \"Brief 2-3 sentence overview of the user\",
  \"engagement_analysis\": \"Analysis of user engagement level\",
  \"health_insights\": \"Health-related observations based on BMI and profile data\",
  \"recommendations\": [
    \"Specific actionable recommendation 1\",
    \"Specific actionable recommendation 2\",
    \"Specific actionable recommendation 3\"
  ],
  \"risk_factors\": [\"Any concerns or risk factors\"],
  \"strengths\": [\"Positive aspects about this user\"],
  \"next_actions\": [\"What admin should do next with this user\"]
}

Be specific, actionable, and professional. Write in French.";

        return $this->callGroqAPI($prompt);
    }

    /**
     * Suggest smart notes based on user data
     */
    public function suggestNotes($userData, $recentActivity, $engagementScore) {
        $prompt = "You are an admin assistant for Nutrimind, a nutrition platform.

Based on this user's profile, suggest 3-5 relevant notes that an admin might want to add:

USER INFO:
- Name: {$userData['nom']}
- Engagement Score: {$engagementScore}%
- Recent Activities: " . count($recentActivity) . "
- Last Login: " . ($userData['last_login'] ?? 'Never') . "
- Profile Status: " . ($userData['status']) . "

Return ONLY a JSON array of note suggestions in French:
[
  \"Note suggestion 1\",
  \"Note suggestion 2\",
  \"Note suggestion 3\"
]

Make notes specific, actionable, and relevant to user management.";

        return $this->callGroqAPI($prompt);
    }

    /**
     * Recommend tags based on user behavior
     */
    public function recommendTags($userData, $activityStats, $engagementScore) {
        $bmi = $this->calculateBMI($userData);
        
        $prompt = "You are a user categorization expert for Nutrimind.

Based on this user profile, recommend 3-5 relevant tags:

USER DATA:
- Engagement Score: {$engagementScore}%
- BMI: " . ($bmi ?? 'Unknown') . "
- Total Activities: {$activityStats['total_activities']}
- Recent Activities: {$activityStats['recent_activities']}
- Has Allergies: " . ($userData['allergique'] == 1 ? 'Yes' : 'No') . "
- Account Status: {$userData['status']}
- Days Since Registration: " . $this->daysSinceRegistration($userData['date_creation']) . "

Return ONLY a JSON array of objects with tag suggestions in French:
[
  {\"name\": \"Tag name\", \"color\": \"primary\", \"reason\": \"Why this tag\"},
  {\"name\": \"Tag name\", \"color\": \"success\", \"reason\": \"Why this tag\"}
]

Available colors: primary, success, danger, warning, info, secondary
Make tags short (1-3 words), relevant, and useful for user management.";

        return $this->callGroqAPI($prompt);
    }

    /**
     * Analyze user behavior patterns
     */
    public function analyzeBehaviorPatterns($activities, $userData) {
        if (empty($activities)) {
            return [
                'success' => true,
                'analysis' => 'Aucune activité à analyser pour le moment.'
            ];
        }

        $activitySummary = array_map(function($activity) {
            return $activity['activity_type'] . ' - ' . $activity['created_at'];
        }, array_slice($activities, 0, 10));

        $prompt = "You are a behavior analyst for Nutrimind.

Analyze these recent user activities and identify patterns:

USER: {$userData['nom']}
RECENT ACTIVITIES:
" . implode("\n", $activitySummary) . "

Provide analysis in JSON format:
{
  \"patterns\": [\"Pattern 1\", \"Pattern 2\"],
  \"insights\": \"Key behavioral insights\",
  \"predictions\": \"What this user is likely to do next\",
  \"concerns\": \"Any concerning patterns or red flags\"
}

Write in French. Be specific and actionable.";

        return $this->callGroqAPI($prompt);
    }

    /**
     * Generate personalized health recommendations
     */
    public function generateHealthRecommendations($userData) {
        $bmi = $this->calculateBMI($userData);
        
        if (!$bmi) {
            return [
                'success' => false,
                'error' => 'Données insuffisantes pour générer des recommandations santé'
            ];
        }

        $prompt = "You are a health advisor for Nutrimind, a nutrition platform.

Generate personalized health recommendations for this user:

USER DATA:
- Age: " . ($userData['age'] ?? 'Unknown') . "
- Weight: {$userData['poids']} kg
- Height: {$userData['taille']} cm
- BMI: {$bmi}
- Has Allergies: " . ($userData['allergique'] == 1 ? 'Yes' : 'No') . "

Provide recommendations in JSON format:
{
  \"bmi_category\": \"Category (Insuffisant/Normal/Surpoids/Obésité)\",
  \"health_status\": \"Overall health assessment\",
  \"recommendations\": [
    \"Specific recommendation 1\",
    \"Specific recommendation 2\",
    \"Specific recommendation 3\"
  ],
  \"nutrition_tips\": [\"Tip 1\", \"Tip 2\"],
  \"exercise_suggestions\": [\"Suggestion 1\", \"Suggestion 2\"],
  \"warnings\": [\"Any health warnings or concerns\"]
}

Write in French. Be professional, supportive, and evidence-based.";

        return $this->callGroqAPI($prompt);
    }

    /**
     * Generate smart summary of user profile
     */
    public function generateProfileSummary($userData, $notes, $tags, $activityStats) {
        $prompt = "You are a user profile summarizer for Nutrimind.

Create a concise, professional summary of this user:

USER: {$userData['nom']} ({$userData['email']})
ROLE: {$userData['role']}
NOTES COUNT: " . count($notes) . "
TAGS COUNT: " . count($tags) . "
TOTAL ACTIVITIES: {$activityStats['total_activities']}

Generate a 2-3 sentence professional summary in French that captures:
- User's engagement level
- Key characteristics
- Current status

Return ONLY the summary text, no JSON.";

        $result = $this->callGroqAPI($prompt);
        
        if ($result['success']) {
            // Extract text from response
            return [
                'success' => true,
                'summary' => $result['content']
            ];
        }
        
        return $result;
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
            'max_tokens' => 1500
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'API request failed: ' . $error
            ];
        }

        if ($httpCode !== 200) {
            return [
                'success' => false,
                'error' => 'API returned error code: ' . $httpCode,
                'response' => $response
            ];
        }

        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            return [
                'success' => false,
                'error' => 'Invalid API response',
                'response' => $response
            ];
        }

        $content = trim($result['choices'][0]['message']['content']);
        
        // Try to parse as JSON
        $jsonContent = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'success' => true,
                'data' => $jsonContent,
                'content' => $content
            ];
        }

        // Return as plain text
        return [
            'success' => true,
            'content' => $content
        ];
    }

    /**
     * Calculate BMI
     */
    private function calculateBMI($userData) {
        if (empty($userData['poids']) || empty($userData['taille'])) {
            return null;
        }
        
        $heightInMeters = $userData['taille'] / 100;
        return round($userData['poids'] / ($heightInMeters * $heightInMeters), 1);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($userData) {
        $fields = ['nom', 'email', 'age', 'poids', 'taille', 'allergique', 'role'];
        $filled = 0;
        
        foreach ($fields as $field) {
            if (!empty($userData[$field]) && $userData[$field] !== null) {
                $filled++;
            }
        }
        
        return round(($filled / count($fields)) * 100);
    }

    /**
     * Calculate days since registration
     */
    private function daysSinceRegistration($dateCreation) {
        $registrationDate = new DateTime($dateCreation);
        $now = new DateTime();
        $interval = $now->diff($registrationDate);
        return $interval->days;
    }
}
?>
