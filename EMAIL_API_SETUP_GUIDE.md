# 📧 Email API Setup Guide

## 🎯 Quick Setup (5 Minutes)

You need to configure an email API to send emails. I recommend **Resend** (easiest and free).

---

## 🚀 Option 1: Resend (RECOMMENDED - Easiest)

### Step 1: Sign Up
1. Go to: https://resend.com/signup
2. Sign up with your email (free)
3. Verify your email

### Step 2: Get API Key
1. After login, go to **API Keys** section
2. Click **"Create API Key"**
3. Give it a name (e.g., "Nutrimind")
4. Click **"Create"**
5. **Copy the API key** (starts with `re_...`)

### Step 3: Configure
1. Open: `config/email_api_config.php`
2. Update these lines:
```php
'provider' => 'resend',
'api_key' => 're_YOUR_API_KEY_HERE',  // Paste your API key
'from_email' => 'onboarding@resend.dev',  // Use this for testing
'from_name' => 'Nutrimind',
```

### Step 4: Test
1. Go to: `http://localhost/nutrimind/test_email_api.php`
2. You should see "✅ Email Sent Successfully!"
3. Check your inbox

### ✅ Done! You can now send emails!

**Free Tier:**
- 100 emails/day
- 3,000 emails/month
- No credit card required

---

## 📧 Option 2: SendGrid

### Step 1: Sign Up
1. Go to: https://signup.sendgrid.com/
2. Sign up (free)
3. Verify your email

### Step 2: Get API Key
1. Go to **Settings** → **API Keys**
2. Click **"Create API Key"**
3. Choose **"Full Access"**
4. Give it a name
5. Click **"Create & View"**
6. **Copy the API key** (starts with `SG.`)

### Step 3: Verify Sender
1. Go to **Settings** → **Sender Authentication**
2. Click **"Verify a Single Sender"**
3. Enter your email (e.g., `rahoui.amine23@gmail.com`)
4. Fill in the form
5. Check your email and verify

### Step 4: Configure
1. Open: `config/email_api_config.php`
2. Update:
```php
'provider' => 'sendgrid',
'api_key' => 'SG.YOUR_API_KEY_HERE',
'from_email' => 'rahoui.amine23@gmail.com',  // Your verified email
'from_name' => 'Nutrimind',
```

### Step 5: Test
1. Go to: `http://localhost/nutrimind/test_email_api.php`
2. Check for success message

**Free Tier:**
- 100 emails/day
- Forever free

---

## 📮 Option 3: Mailgun

### Step 1: Sign Up
1. Go to: https://signup.mailgun.com/
2. Sign up (free trial)
3. Verify your email

### Step 2: Get API Key
1. Go to **Settings** → **API Keys**
2. Copy the **Private API key**

### Step 3: Get Domain
1. Go to **Sending** → **Domains**
2. Copy your sandbox domain (e.g., `sandboxXXX.mailgun.org`)

### Step 4: Configure
1. Open: `config/email_api_config.php`
2. Update:
```php
'provider' => 'mailgun',
'api_key' => 'YOUR_API_KEY_HERE',
'from_email' => 'postmaster@sandboxXXX.mailgun.org',
'from_name' => 'Nutrimind',
'mailgun_domain' => 'sandboxXXX.mailgun.org',
```

### Step 5: Test
1. Go to: `http://localhost/nutrimind/test_email_api.php`
2. Check for success message

**Free Trial:**
- 5,000 emails/month for 3 months

---

## 🔧 Configuration File

Edit: `config/email_api_config.php`

```php
<?php
return [
    // Choose: 'resend', 'sendgrid', or 'mailgun'
    'provider' => 'resend',
    
    // Your API key
    'api_key' => '',  // ADD YOUR KEY HERE
    
    // From email (must be verified)
    'from_email' => 'onboarding@resend.dev',
    'from_name' => 'Nutrimind',
    
    // Mailgun only
    'mailgun_domain' => '',
];
?>
```

---

## 🧪 Testing

### Test Your Configuration
```
http://localhost/nutrimind/test_email_api.php
```

This will:
- Show your current configuration
- Send a test email
- Confirm if it's working

---

## ✅ What You Get

Once configured, you can:
- ✅ Send AI-generated personalized emails
- ✅ Send individual emails
- ✅ Send bulk emails
- ✅ Track delivery status
- ✅ View campaign history

---

## 🎯 Comparison

| Provider | Free Tier | Ease | Speed |
|----------|-----------|------|-------|
| **Resend** | 3,000/month | ⭐⭐⭐⭐⭐ | Fast |
| **SendGrid** | 100/day | ⭐⭐⭐⭐ | Fast |
| **Mailgun** | 5,000/3mo | ⭐⭐⭐ | Fast |

**Recommendation: Use Resend** (easiest setup, no verification needed for testing)

---

## 🐛 Troubleshooting

### Error: "API Key Not Set"
**Solution**: Add your API key to `config/email_api_config.php`

### Error: "Email Sending Failed"
**Solutions**:
1. Check API key is correct
2. Check provider name is correct
3. For SendGrid: Verify your sender email
4. For Mailgun: Check domain is correct
5. Check internet connection

### Error: "From email not verified"
**Solution**: 
- Resend: Use `onboarding@resend.dev` for testing
- SendGrid: Verify your sender email first
- Mailgun: Use your sandbox domain

---

## 💡 Pro Tips

1. **Start with Resend** - No verification needed for testing
2. **Test first** - Always run `test_email_api.php` before using
3. **Check spam** - Test emails might go to spam
4. **Use your domain** - For production, use your own domain
5. **Monitor usage** - Check your provider dashboard for limits

---

## 🎉 Ready!

Once you see "✅ Email Sent Successfully!" in the test, you're ready to:

1. Go to Users page
2. Select users
3. Generate AI emails
4. Click "Send Email"
5. Done! 🚀

---

## 📞 Need Help?

- **Resend Docs**: https://resend.com/docs
- **SendGrid Docs**: https://docs.sendgrid.com/
- **Mailgun Docs**: https://documentation.mailgun.com/

---

**Recommended: Resend** - Sign up at https://resend.com/signup (takes 2 minutes)
