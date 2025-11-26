# 🔍 SMS Credentials Logging Guide

## 📊 What Logs to Expect

Your SMS system now has **comprehensive logging** to tell you exactly what's wrong with your credentials!

---

## ✅ Scenario 1: Credentials ARE Correct

### What You'll See in Logs:

```log
[time] local.INFO: HostPinnacleSmsService initialized {
    "user_id_set": true,
    "user_id_value": "mad***",
    "password_set": true,
    "password_length": 12,
    "sender_id": "SHOPYBOOK",
    "sender_id_set": true,
    "api_url": "https://smsportal.hostpinnacle.co.ke/SMSApi/send",
    "api_key_set": false,
    "configured": true
}

[time] local.INFO: Sending SMS via Host Pinnacle {
    "recipients_count": 2,
    "message_length": 35,
    "send_method": "quick"
}

[time] local.INFO: Host Pinnacle SMS Response {
    "status": 200,
    "response_preview": "Success: SMS sent",
    "response_length": 150
}

[time] local.INFO: SMS sent successfully {
    "recipients": 2,
    "api_response": {...}
}
```

**✅ Success! Everything is working.**

---

## ❌ Scenario 2: Credentials NOT Set (Missing from .env)

### What You'll See:

```log
[time] local.INFO: HostPinnacleSmsService initialized {
    "user_id_set": false,
    "user_id_value": "NOT SET",
    "password_set": false,
    "password_length": 0,
    "sender_id": "SHOPYBOOK",
    "sender_id_set": true,
    "configured": false
}

[time] local.WARNING: SMS service configuration incomplete {
    "user_id_missing": true,
    "password_missing": true,
    "sender_id_missing": false,
    "help": "Add HOSTPINNACLE_USER_ID, HOSTPINNACLE_PASSWORD, and HOSTPINNACLE_SENDER_ID to your .env file"
}

[time] local.ERROR: SMS service not configured {
    "user_id": 5,
    "business_id": "e69dc410..."
}
```

**❌ Problem: Credentials are missing from .env file**

**Solution:**
1. Add credentials to `.env` file
2. Run `php artisan config:clear`
3. Try again

---

## ❌ Scenario 3: Wrong Username (USER_ID)

### What You'll See:

```log
[time] local.INFO: HostPinnacleSmsService initialized {
    "user_id_set": true,
    "user_id_value": "wro***",  ← Wrong username
    "password_set": true,
    "password_length": 12,
    "configured": true
}

[time] local.INFO: Sending SMS via Host Pinnacle {...}

[time] local.INFO: Host Pinnacle SMS Response {
    "status": 200,
    "response_preview": "Error: Invalid user ID",
    "response_length": 50
}

[time] local.ERROR: SMS API Error Detected: INVALID USERNAME {
    "error_type": "INVALID USERNAME",
    "http_status": null,
    "credentials_used": {
        "user_id": "wro***",
        "password_length": 12,
        "sender_id": "SHOPYBOOK"
    },
    "api_response": "Error: Invalid user ID",
    "solution": "Username (USER_ID) is wrong. Check HOSTPINNACLE_USER_ID in .env file"
}

[time] local.CRITICAL: 🔴 CREDENTIAL ERROR - SMS CANNOT BE SENT {
    "problem": "Your Host Pinnacle credentials are WRONG or MISSING",
    "current_config": {
        "user_id": "wronguser",
        "password_set": "YES (length: 12)",
        "sender_id": "SHOPYBOOK"
    },
    "action_required": [
        "1. Check your .env file",
        "2. Verify credentials at https://smsportal.hostpinnacle.co.ke",
        "3. Run: php artisan config:clear",
        "4. Restart your server"
    ]
}
```

**❌ Problem: Username is WRONG**

**Solution:**
1. Fix `HOSTPINNACLE_USER_ID` in `.env` file
2. Verify at https://smsportal.hostpinnacle.co.ke
3. Run `php artisan config:clear`

---

## ❌ Scenario 4: Wrong Password

### What You'll See:

```log
[time] local.INFO: HostPinnacleSmsService initialized {
    "user_id_set": true,
    "user_id_value": "mad***",
    "password_set": true,
    "password_length": 15,  ← Wrong password
    "configured": true
}

[time] local.INFO: Host Pinnacle SMS Response {
    "status": 200,
    "response_preview": "Error: Invalid password",
}

[time] local.ERROR: SMS API Error Detected: INVALID PASSWORD {
    "error_type": "INVALID PASSWORD",
    "credentials_used": {
        "user_id": "mad***",
        "password_length": 15,
        "sender_id": "SHOPYBOOK"
    },
    "solution": "Password is wrong. Check HOSTPINNACLE_PASSWORD in .env file"
}

[time] local.CRITICAL: 🔴 CREDENTIAL ERROR - SMS CANNOT BE SENT {
    "problem": "Your Host Pinnacle credentials are WRONG or MISSING",
    "current_config": {
        "user_id": "madgenius",
        "password_set": "YES (length: 15)",
        "sender_id": "SHOPYBOOK"
    }
}
```

**❌ Problem: Password is WRONG**

**Solution:**
1. Fix `HOSTPINNACLE_PASSWORD` in `.env` file
2. Verify password works at https://smsportal.hostpinnacle.co.ke
3. Run `php artisan config:clear`

---

## ❌ Scenario 5: Invalid or Unapproved Sender ID

### What You'll See:

```log
[time] local.ERROR: SMS API Error Detected: INVALID SENDER ID {
    "error_type": "INVALID SENDER ID",
    "credentials_used": {
        "sender_id": "BADNAME"
    },
    "solution": "Sender ID is not approved or invalid. Check HOSTPINNACLE_SENDER_ID in .env file and ensure it is approved in Host Pinnacle dashboard"
}
```

**❌ Problem: Sender ID is NOT APPROVED**

**Solution:**
1. Login to Host Pinnacle dashboard
2. Go to: Account → SenderId
3. Check if your Sender ID is approved
4. If not approved, create and wait for approval
5. Update `.env` with approved Sender ID

---

## ❌ Scenario 6: Authentication Failed (General)

### What You'll See:

```log
[time] local.ERROR: SMS API Error Detected: AUTHENTICATION FAILED {
    "error_type": "AUTHENTICATION FAILED",
    "http_status": 401,
    "solution": "Your username or password is incorrect. Check HOSTPINNACLE_USER_ID and HOSTPINNACLE_PASSWORD in .env file"
}

[time] local.CRITICAL: 🔴 CREDENTIAL ERROR - SMS CANNOT BE SENT
```

**❌ Problem: Username OR password is wrong**

**Solution:**
1. Double-check both username AND password
2. Try logging into https://smsportal.hostpinnacle.co.ke manually
3. Update `.env` with correct credentials

---

## ⚠️ Scenario 7: Insufficient Credits

### What You'll See:

```log
[time] local.ERROR: SMS API Error Detected: INSUFFICIENT CREDITS {
    "error_type": "INSUFFICIENT CREDITS",
    "solution": "Your SMS account has insufficient credits. Top up your account at Host Pinnacle dashboard"
}
```

**⚠️ Problem: No SMS credits left**

**Solution:**
1. Login to Host Pinnacle dashboard
2. Check your credit balance
3. Top up your account

---

## 🔍 How to View These Logs

### Method 1: Real-time monitoring
```bash
# View all logs
tail -f storage/logs/laravel.log

# View only SMS-related logs
tail -f storage/logs/laravel.log | grep -E "(SMS|HostPinnacle)"

# View only errors
tail -f storage/logs/laravel.log | grep ERROR

# View only critical errors
tail -f storage/logs/laravel.log | grep CRITICAL
```

### Method 2: Open log file
Open: `storage/logs/laravel.log` in your code editor

Search for today's date: `[2025-10-08]`

### Method 3: Filter by type
```bash
# Configuration issues
grep "configuration incomplete" storage/logs/laravel.log

# Credential errors
grep "CREDENTIAL ERROR" storage/logs/laravel.log

# API errors
grep "SMS API Error" storage/logs/laravel.log
```

---

## 📋 Quick Diagnostic Checklist

When you see an error, check these logs in order:

1. **HostPinnacleSmsService initialized**
   - ✅ All values should be `true` and properly set
   - ❌ If `NOT SET`, credentials are missing

2. **SMS service configuration incomplete**
   - Lists exactly which credentials are missing

3. **SMS API Error Detected**
   - Shows exactly what's wrong (username, password, sender ID, etc.)

4. **🔴 CREDENTIAL ERROR**
   - **CRITICAL** alert with step-by-step fix instructions

---

## 🎯 Example: Complete Log Flow

### Successful Send:
```
✅ HostPinnacleSmsService initialized (all credentials set)
✅ Sending SMS via Host Pinnacle (2 recipients)
✅ Host Pinnacle SMS Response (status: 200)
✅ SMS sent successfully
```

### Failed Send (Wrong Password):
```
✅ HostPinnacleSmsService initialized (credentials set)
✅ Sending SMS via Host Pinnacle
❌ Host Pinnacle SMS Response (error message)
❌ SMS API Error Detected: INVALID PASSWORD
🔴 CREDENTIAL ERROR - SMS CANNOT BE SENT
```

---

## 💡 Pro Tips

1. **Check initialization logs first** - They tell you if credentials are loaded
2. **Look for CRITICAL logs** - These are credential errors
3. **Read the "solution" field** - It tells you exactly what to fix
4. **Check "current_config"** - Shows what values are actually being used
5. **Follow "action_required"** - Step-by-step fix instructions

---

## 🆘 Still Having Issues?

If logs show credentials are correct but SMS still fails:

1. Verify credentials work on Host Pinnacle website
2. Check if your account is active/not suspended
3. Ensure you have SMS credits
4. Check if your Sender ID is approved
5. Contact Host Pinnacle support

---

**You now have complete visibility into your SMS credentials! 🎉**


