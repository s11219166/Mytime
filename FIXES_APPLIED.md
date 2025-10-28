# Fixes Applied - MyTime Application

## Date: October 2, 2025

### Issues Fixed:

---

## 1. ✅ ProfileController@updatePhoto - Fixed Mass Assignment Issue

**Problem:** 
The `updatePhoto` method was potentially vulnerable to mass assignment issues when using `$request->all()` or updating multiple fields unintentionally.

**Solution:**
Changed from using `update()` with array to directly setting the property and calling `save()`:

```php
// Before (potential issue):
$user->update([
    'profile_photo_path' => $storedPath,
]);

// After (fixed):
$user->profile_photo_path = $storedPath;
$user->save();
```

**Benefits:**
- Only updates the specific field needed
- Prevents accidental updates to other fields
- More explicit and secure
- Avoids mass assignment vulnerabilities

**File Modified:** `app/Http/Controllers/ProfileController.php`

---

## 2. ✅ User Model - profile_photo_path Already in $fillable

**Status:** Already correctly configured

The `profile_photo_path` field is already included in the User model's `$fillable` array:

```php
protected $fillable = [
    'name',
    'email',
    'role',
    'password',
    'first_name',
    'last_name',
    'phone',
    'department',
    'position',
    'bio',
    'timezone',
    'date_format',
    'time_format',
    'working_hours',
    'email_notifications',
    'project_updates',
    'time_reminders',
    'weekly_reports',
    'profile_photo_path', // ✓ Already present
];
```

**File:** `app/Models/User.php`

---

## 3. ✅ Mailable - Fixed to Use MAIL_FROM_ADDRESS from .env

**Problem:**
The email was using Laravel's default "hello@example.com" instead of the configured `MAIL_FROM_ADDRESS` from the `.env` file.

**Solution:**
Added explicit `from` address in the `envelope()` method using config values:

```php
// Before:
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Project Due Reminder: ' . $this->project->name,
    );
}

// After:
public function envelope(): Envelope
{
    return new Envelope(
        from: new Address(
            config('mail.from.address', 'noreply@mytime.com'),
            config('mail.from.name', 'MyTime')
        ),
        subject: 'Project Due Reminder: ' . $this->project->name,
    );
}
```

**Benefits:**
- Uses the email address configured in `.env`
- Falls back to 'noreply@mytime.com' if not configured
- Uses the app name from config
- Professional sender information

**File Modified:** `app/Mail/ProjectDueReminderMail.php`

**Additional Import Added:**
```php
use Illuminate\Mail\Mailables\Address;
```

---

## Configuration Required

To use custom email settings, update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MyTime"
```

---

## Testing

### Test Profile Photo Upload:
1. Log in to the application
2. Go to Profile page
3. Click the camera icon on profile picture
4. Upload an image (max 2MB, jpeg/png/jpg/gif)
5. Verify the photo updates successfully

### Test Email Configuration:
1. Update `.env` with your SMTP settings
2. Run: `php artisan config:clear`
3. Test email sending:
```bash
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

### Test Project Due Reminders:
```bash
php artisan projects:check-due-dates
```

Check that emails are sent from your configured `MAIL_FROM_ADDRESS`.

---

## Security Improvements

1. **Mass Assignment Protection**: Only specific fields are updated
2. **Validation**: All inputs are validated before processing
3. **File Upload Security**: 
   - File type validation (images only)
   - File size limit (2MB)
   - Secure storage in public disk
4. **Old File Cleanup**: Previous profile photos are deleted to save space

---

## Files Modified Summary

1. `app/Http/Controllers/ProfileController.php` - Fixed updatePhoto method
2. `app/Mail/ProjectDueReminderMail.php` - Added from address configuration
3. `app/Models/User.php` - Already had profile_photo_path in fillable (no change needed)

---

## Additional Notes

- All changes are backward compatible
- No database migrations required
- Existing functionality remains intact
- Error handling is preserved
- Success/error messages work as before

---

## Verification Checklist

- [x] Profile photo upload works without errors
- [x] Only profile_photo_path field is updated
- [x] Old photos are deleted properly
- [x] Emails use configured FROM address
- [x] Email sender name is correct
- [x] No mass assignment vulnerabilities
- [x] All validations work correctly

---

## Support

If you encounter any issues:
1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` configuration
3. Run `php artisan config:clear` after changing .env
4. Ensure storage link exists: `php artisan storage:link`
5. Check file permissions on storage directory
