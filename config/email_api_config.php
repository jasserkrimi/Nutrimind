<?php

/**
 * Email API Configuration
 * 
 * Choose your email provider and add your API key
 */

return [
    // ============================================
    // CHOOSE YOUR EMAIL PROVIDER
    // ============================================
    // Options: 'resend', 'sendgrid', 'mailgun'
    'provider' => 'resend',  // RECOMMENDED: Resend is easiest
    
    // ============================================
    // API KEY (Get from your provider)
    // ============================================
    'api_key' => '',  // ADD YOUR API KEY HERE
    
    // ============================================
    // FROM EMAIL (Your verified sender email)
    // ============================================
    'from_email' => 'onboarding@resend.dev',  // For Resend testing, or use your domain
    'from_name' => 'Nutrimind',
    
    // ============================================
    // MAILGUN ONLY (if using Mailgun)
    // ============================================
    'mailgun_domain' => '',  // e.g., 'mg.yourdomain.com'
];

/**
 * ============================================
 * HOW TO GET API KEYS:
 * ============================================
 * 
 * RESEND (Recommended - Easiest):
 * 1. Go to: https://resend.com/signup
 * 2. Sign up (free)
 * 3. Go to API Keys section
 * 4. Create new API key
 * 5. Copy and paste above
 * 6. Free tier: 100 emails/day, 3,000/month
 * 
 * SENDGRID:
 * 1. Go to: https://signup.sendgrid.com/
 * 2. Sign up (free)
 * 3. Go to Settings > API Keys
 * 4. Create API Key
 * 5. Copy and paste above
 * 6. Free tier: 100 emails/day
 * 
 * MAILGUN:
 * 1. Go to: https://signup.mailgun.com/
 * 2. Sign up (free trial)
 * 3. Go to API Keys section
 * 4. Copy API key
 * 5. Add your domain
 * 6. Copy and paste above
 * 7. Free trial: 5,000 emails/month
 */
?>
