# Login Credentials

## Your Updated Account

**Email**: salvesh2004@gmail.com
**Password**: (your existing password - unchanged)

**Note**: Only the email address was updated. Your password remains the same.

## If You Need to Reset Password

If you've forgotten your password, you can reset it using:

```bash
php artisan tinker
$user = App\Models\User::where('email', 'salvesh2004@gmail.com')->first();
$user->password = bcrypt('your-new-password');
$user->save();
```

Replace `'your-new-password'` with your desired password.

## All Users in System

1. **Salvesh Chand** (Admin)
   - Email: salvesh2004@gmail.com
   - Role: admin
   
2. **John Doe** (User)
   - Email: user@mytime.com
   - Role: user

---

*Your email notifications are being sent to: salvesh2004@gmail.com*
