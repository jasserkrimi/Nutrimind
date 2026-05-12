# 🤖 AI Email Campaign System - User Guide

## 🎯 Overview

The AI Email Campaign System uses **Groq AI (Llama 3.1 70B)** to generate personalized emails for your users based on their profile data, activity, and health metrics.

---

## 🚀 Quick Start

### Step 1: Run Database Migration

1. Open your browser and go to:
   ```
   http://localhost/nutrimind/run_email_campaigns_migration.php
   ```

2. This will create two new tables:
   - `email_campaigns` - Stores all sent emails
   - `user_segments` - Tracks user segmentation

3. Click "Go to Users Page" when migration completes

---

### Step 2: Access the AI Email Campaign

1. Go to: `http://localhost/nutrimind/views/backoffice/users.php`

2. Scroll down to the **"🤖 AI Email Campaign System"** card

3. Click the toggle button to expand the section

---

## 📊 User Segmentation

The system automatically segments users into 7 categories:

| Segment | Description | Criteria |
|---------|-------------|----------|
| 🔥 **Super Active** | Highly engaged users | Logged in last 7 days + 80%+ profile complete |
| ⭐ **VIP Users** | Admin accounts | Role = admin |
| 🆕 **New Users** | Recently registered | Registered < 7 days ago |
| 💤 **Dormant** | Inactive users | No login for 14-30 days |
| ⚠️ **At Risk** | Very inactive | No login for 30+ days |
| 📝 **Incomplete Profile** | Missing data | Profile < 70% complete |
| 🚨 **Allergy Alert** | Users with allergies | allergique = 1 |

---

## ✉️ Email Templates

8 AI-powered templates available:

1. **🎉 Welcome Email** - For new users
2. **🔥 Re-engagement** - Win back inactive users
3. **📝 Profile Completion** - Encourage completing profile
4. **💪 Health Milestone** - Celebrate achievements
5. **⚠️ Allergy Safety Alert** - Safety reminders
6. **🎂 Birthday Wishes** - Birthday greetings
7. **📊 Progress Report** - Activity summary
8. **💡 Health Tips** - Personalized advice

---

## 🎨 Email Tones

Choose the tone for AI-generated emails:

- **😊 Friendly** - Casual, warm, approachable
- **👔 Professional** - Formal, business-like
- **💪 Motivational** - Inspiring, energetic
- **💚 Caring** - Empathetic, supportive

---

## 🔧 How to Use

### Generate & Send Emails

1. **Select Segment**: Choose which users to target
2. **Select Template**: Pick email type
3. **Select Tone**: Choose writing style
4. **Click "Generate AI Emails"**: AI creates personalized emails
5. **Review Previews**: Check each email
6. **Send**: 
   - Click "Send" on individual emails, OR
   - Click "Send All Emails" for bulk sending

---

## 🧠 AI Personalization

Each email is personalized with:

### Basic Data
- User's name
- Email address
- Role (admin/user)
- Days since registration
- Last login date

### Advanced Metrics
- **BMI** - Calculated from weight/height
- **BMI Category** - Underweight, Healthy, Overweight, Obese
- **Profile Completion** - Percentage of filled fields
- **Engagement Status** - Super Active, Active, Dormant, At Risk
- **Missing Fields** - What data they haven't provided
- **Allergy Status** - Whether they have allergies

### Smart Content
- Health tips based on BMI
- Personalized goals
- Activity-based messaging
- Safety warnings for allergies

---

## 📈 Campaign History

Click **"History"** button to view:
- All sent emails
- Delivery status (sent/failed)
- AI-generated vs manual emails
- Timestamps
- Recipients

---

## 🔐 API Configuration

The system uses **Groq AI API**:

- **API Key**: Already configured in `config/AIEmailGenerator.php`
- **Model**: llama-3.1-70b-versatile
- **Rate Limit**: 30 requests/minute (free tier)
- **Cost**: FREE

---

## 📧 Email Sending

Emails are sent via **SMTP** using your configured email settings:

- Configuration file: `config/email_config.php`
- Email class: `config/EmailSimple.php`
- Make sure your Gmail App Password is configured

---

## 💡 Best Practices

### 1. Segment Wisely
- Target specific user groups for better engagement
- Don't spam all users at once

### 2. Review Before Sending
- Always preview AI-generated emails
- Edit subject lines if needed
- Check for accuracy

### 3. Timing Matters
- Send re-engagement emails to dormant users
- Welcome emails to new users within 24 hours
- Health tips to active users weekly

### 4. Track Results
- Check campaign history regularly
- Monitor delivery success rate
- Adjust strategy based on results

---

## 🎯 Example Use Cases

### Use Case 1: Welcome New Users
```
Segment: New Users
Template: Welcome Email
Tone: Friendly
Result: Warm welcome + profile completion encouragement
```

### Use Case 2: Re-engage Inactive Users
```
Segment: At Risk
Template: Re-engagement
Tone: Motivational
Result: Compelling reason to return + benefits reminder
```

### Use Case 3: Health Tips for Active Users
```
Segment: Super Active
Template: Health Tips
Tone: Professional
Result: Personalized nutrition advice based on BMI
```

### Use Case 4: Safety Alert for Allergy Users
```
Segment: Allergy Alert
Template: Allergy Safety Alert
Tone: Caring
Result: Safety reminders + profile update prompt
```

---

## 🐛 Troubleshooting

### AI Generation Fails
- **Check**: Internet connection
- **Check**: Groq API status
- **Solution**: Try again in a few seconds

### Email Not Sending
- **Check**: `config/email_config.php` settings
- **Check**: Gmail App Password configured
- **Check**: SMTP settings correct
- **Solution**: Test with `test_email_simple.php`

### No Users in Segment
- **Check**: User data in database
- **Check**: Segment criteria (dates, profile completion)
- **Solution**: Try "All Users" segment

---

## 📊 Database Schema

### email_campaigns Table
```sql
- id: Campaign ID
- user_id: Recipient user ID
- template_name: Template used
- subject: Email subject
- content: Email body (HTML)
- ai_generated: 1 if AI-generated, 0 if manual
- status: sent/failed/pending
- sent_at: Timestamp
```

### user_segments Table
```sql
- id: Segment ID
- user_id: User ID
- segment_type: Segment name
- assigned_at: Timestamp
```

---

## 🎉 Features Summary

✅ **AI-Powered** - Real AI writes unique emails  
✅ **Personalized** - Uses user data (BMI, activity, profile)  
✅ **Segmented** - Target specific user groups  
✅ **8 Templates** - Pre-built for common scenarios  
✅ **4 Tones** - Adapt writing style  
✅ **Bulk Sending** - Send to multiple users at once  
✅ **Preview** - Review before sending  
✅ **History** - Track all campaigns  
✅ **Free** - Uses free Groq API  

---

## 🚀 Next Steps

1. Run the migration
2. Generate your first AI email
3. Send to a test user
4. Review the results
5. Scale up to bulk campaigns

---

## 📞 Support

If you encounter issues:
1. Check the browser console for errors
2. Check PHP error logs
3. Verify database tables exist
4. Test email configuration

---

**Built with ❤️ using Groq AI (Llama 3.1 70B)**
