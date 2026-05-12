# ✅ Activate Email Sending - Quick Guide

## 🎯 What You Need

To send emails, you need an **Email API** (recommended) or **Gmail SMTP**.

---

## 🚀 RECOMMENDED: Email API (Easiest)

### Why API?
- ✅ No Gmail App Password needed
- ✅ Easier setup (2 minutes)
- ✅ More reliable
- ✅ Better deliverability
- ✅ Free tier available

---

## 📧 Quick Setup (2 Minutes)

### Step 1: Sign Up for Resend (FREE)
```
https://resend.com/signup
```

### Step 2: Get API Key
1. After signup, go to **API Keys**
2. Click **"Create API Key"**
3. Copy the key (starts with `re_...`)

### Step 3: Configure
Open: `config/email_api_config.php`

Change this line:
```php
'api_key' => '',  // Paste your API key here
```

To:
```php
'api_key' => 're_YOUR_ACTUAL_KEY_HERE',
```

### Step 4: Test
Go to:
```
http://localhost/nutrimind/test_email_api.php
```

You should see: **"✅ Email Sent Successfully!"**

### ✅ Done! You can now send emails!

---

## 📊 What's Configured

The system will:
1. Try to send via **Email API** (Resend, SendGrid, or Mailgun)
2. If API fails, fallback to **SMTP** (Gmail)
3. Log all sent emails
4. Track delivery status

---

## 🎯 Files Created

✅ `config/EmailAPI.php` - Email API handler  
✅ `config/email_api_config.php` - Configuration file  
✅ `test_email_api.php` - Test script  
✅ `EMAIL_API_SETUP_GUIDE.md` - Detailed guide  

---

## 🔧 Configuration Options

### Option 1: Resend (Recommended)
```php
'provider' => 'resend',
'api_key' => 're_YOUR_KEY',
'from_email' => 'onboarding@resend.dev',
```
**Free**: 3,000 emails/month

### Option 2: SendGrid
```php
'provider' => 'sendgrid',
'api_key' => 'SG.YOUR_KEY',
'from_email' => 'your@email.com',
```
**Free**: 100 emails/day

### Option 3: Mailgun
```php
'provider' => 'mailgun',
'api_key' => 'YOUR_KEY',
'from_email' => 'postmaster@sandbox.mailgun.org',
'mailgun_domain' => 'sandbox.mailgun.org',
```
**Free Trial**: 5,000 emails/3 months

---

## 🧪 Testing

### Test Email API
```
http://localhost/nutrimind/test_email_api.php
```

### Test AI Email Generation
```
http://localhost/nutrimind/test_ai_email.php
```

---

## ✅ How to Use

Once configured:

1. **Go to Users Page**
   ```
   http://localhost/nutrimind/views/backoffice/users.php
   ```

2. **Expand AI Email Campaign Section**
   - Click toggle button

3. **Select Users**
   - Choose segment
   - Select template & tone
   - Click "Show Users"
   - Check users to email

4. **Generate AI Emails**
   - Click "Generate AI Emails for Selected Users"
   - Wait for AI to generate

5. **Send Emails**
   - Review generated emails
   - Click "Send Email" on each
   - Or click "Send All Emails"

---

## 🎉 Features

✅ **AI-Generated** - Unique email for each user  
✅ **Personalized** - Uses BMI, activity, profile  
✅ **API-Powered** - Fast and reliable  
✅ **Fallback** - Uses SMTP if API fails  
✅ **Tracking** - Logs all sent emails  
✅ **History** - View campaign history  

---

## 📈 Free Tier Limits

| Provider | Daily | Monthly |
|----------|-------|---------|
| Resend | 100 | 3,000 |
| SendGrid | 100 | 3,000 |
| Mailgun | 166 | 5,000 (3 months) |

---

## 🐛 Troubleshooting

### "API Key Not Set"
**Fix**: Add API key to `config/email_api_config.php`

### "Email Sending Failed"
**Fix**: 
1. Check API key is correct
2. Run `test_email_api.php`
3. Check internet connection

### "Generation Failed"
**Fix**: Already fixed! Model updated to `llama-3.3-70b-versatile`

---

## 💡 Quick Start Checklist

- [ ] Sign up for Resend (2 min)
- [ ] Get API key
- [ ] Add to `config/email_api_config.php`
- [ ] Test with `test_email_api.php`
- [ ] Go to Users page
- [ ] Generate and send emails!

---

## 🎯 Summary

**What you need:**
1. Resend account (free)
2. API key (copy/paste)
3. 2 minutes

**What you get:**
- AI-powered personalized emails
- Reliable delivery
- Campaign tracking
- 3,000 free emails/month

---

## 🚀 Get Started Now!

1. **Sign up**: https://resend.com/signup
2. **Get API key**: Copy from dashboard
3. **Configure**: Paste in `config/email_api_config.php`
4. **Test**: Run `test_email_api.php`
5. **Send**: Use AI Email Campaign!

---

**Need detailed instructions?** Read `EMAIL_API_SETUP_GUIDE.md`

**Ready to send emails!** 🎉
