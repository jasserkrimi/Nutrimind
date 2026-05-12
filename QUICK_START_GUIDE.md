# 🚀 Quick Start Guide - AI Email Campaign System

## ✅ What's Ready

Your AI-powered email campaign system is now complete! Here's what you can do:

---

## 📋 Step-by-Step Usage

### Step 1: Run Database Migration
Open in browser:
```
http://localhost/nutrimind/run_email_campaigns_migration.php
```
Click through to complete the setup.

---

### Step 2: Access the System
Go to:
```
http://localhost/nutrimind/views/backoffice/users.php
```

---

### Step 3: Use the AI Email Campaign

1. **Scroll down** to the "🤖 AI Email Campaign System" card
2. **Click the toggle button** to expand it

3. **Select User Segment** (Step 1)
   - Choose from 7 segments or "All Users"
   - See how many users are in each segment

4. **Select Email Template** (Step 2)
   - 8 templates available (Welcome, Re-engagement, etc.)

5. **Select Tone** (Step 3)
   - Friendly, Professional, Motivational, or Caring

6. **Click "Show Users"**
   - See all users in the selected segment

7. **Select Users**
   - ✅ Check the boxes next to users you want to email
   - Use "Select All" or "Deselect All" buttons
   - See count of selected users

8. **Click "Generate AI Emails for Selected Users"**
   - AI generates personalized email for each selected user
   - Takes 2-5 seconds per user

9. **Review Emails**
   - See subject and body for each email
   - Edit subject lines if needed
   - Preview HTML content

10. **Send Emails**
    - Click "Send" on individual emails, OR
    - Click "Send All Emails" for bulk sending

---

## 🎯 Example Workflow

### Re-engage Inactive Users

```
1. Segment: "At Risk" (30+ days inactive)
2. Template: "Re-engagement"
3. Tone: "Motivational"
4. Click "Show Users"
5. Select the users you want to re-engage
6. Generate AI Emails
7. Review and Send
```

### Welcome New Users

```
1. Segment: "New Users" (< 7 days)
2. Template: "Welcome Email"
3. Tone: "Friendly"
4. Click "Show Users"
5. Select all new users
6. Generate AI Emails
7. Review and Send
```

---

## 📊 User Segments Explained

| Segment | Who's Included |
|---------|----------------|
| 🔥 Super Active | Logged in last 7 days + 80%+ profile complete |
| ⭐ VIP Users | Admin accounts |
| 🆕 New Users | Registered < 7 days ago |
| 💤 Dormant | No login for 14-30 days |
| ⚠️ At Risk | No login for 30+ days |
| 📝 Incomplete Profile | Profile < 70% complete |
| 🚨 Allergy Alert | Users with allergies |
| 👥 All Users | Everyone |

---

## ✉️ Email Templates

1. **🎉 Welcome Email** - Onboard new users
2. **🔥 Re-engagement** - Win back inactive users
3. **📝 Profile Completion** - Encourage completing profile
4. **💪 Health Milestone** - Celebrate achievements
5. **⚠️ Allergy Safety Alert** - Safety reminders
6. **🎂 Birthday Wishes** - Birthday greetings
7. **📊 Progress Report** - Activity summary
8. **💡 Health Tips** - Personalized advice

---

## 🤖 AI Personalization

Each email is unique and includes:

- User's name
- BMI (calculated from weight/height)
- Profile completion percentage
- Days since registration
- Last login date
- Engagement status
- Missing profile fields
- Allergy information

---

## 📧 Email Configuration

Make sure your email is configured in:
```
config/email_config.php
```

Set:
- `enabled` = true
- Gmail App Password (get from Google)

Test with:
```
http://localhost/nutrimind/test_email_simple.php
```

---

## 🧪 Test the AI

Before using, test the AI API:
```
http://localhost/nutrimind/test_ai_email.php
```

This verifies the Groq AI is working.

---

## 📈 View Campaign History

Click the **"History"** button to see:
- All sent emails
- Delivery status
- AI-generated vs manual
- Timestamps

---

## 💡 Tips

1. **Start Small**: Test with 1-2 users first
2. **Review Before Sending**: Always check AI-generated emails
3. **Edit Subject Lines**: Customize if needed
4. **Use Segments Wisely**: Target specific user groups
5. **Check History**: Monitor delivery success

---

## 🐛 Troubleshooting

### AI Generation Fails
- Check internet connection
- Try again in a few seconds
- Run `test_ai_email.php` to verify API

### Email Not Sending
- Check `config/email_config.php`
- Verify Gmail App Password
- Test with `test_email_simple.php`

### No Users in Segment
- Check user data in database
- Try "All Users" segment

---

## 🎉 Features

✅ **User Selection Interface** - See and select users visually  
✅ **AI-Powered** - Unique emails for each user  
✅ **Personalized** - Uses BMI, activity, profile data  
✅ **7 Segments** - Intelligent user categorization  
✅ **8 Templates** - Pre-built scenarios  
✅ **4 Tones** - Adapt writing style  
✅ **Bulk Sending** - Send to multiple users  
✅ **Preview** - Review before sending  
✅ **History** - Track all campaigns  
✅ **Free** - Uses free Groq API  

---

## 📞 Need Help?

Check these files:
- `AI_EMAIL_CAMPAIGN_GUIDE.md` - Detailed guide
- `AI_EMAIL_SYSTEM_README.md` - Technical documentation
- `test_ai_email.php` - Test AI API
- `test_email_simple.php` - Test email sending

---

## 🚀 You're Ready!

1. Run migration
2. Go to users page
3. Expand AI Email Campaign section
4. Select segment
5. Select users
6. Generate emails
7. Send!

**Enjoy your AI-powered email system! 🎉**
