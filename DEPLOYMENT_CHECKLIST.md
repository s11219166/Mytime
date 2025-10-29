# ✅ Render.com Deployment Checklist

Use this checklist to ensure smooth deployment.

---

## Before Deployment

### Local Preparation

- [ ] All code tested locally
- [ ] `.env.example` updated with all required variables
- [ ] Database migrations tested
- [ ] Seeders working correctly
- [ ] Assets build successfully (`npm run build`)
- [ ] No sensitive data in code (passwords, keys, etc.)

### GitHub Repository

- [ ] All files committed
- [ ] `.gitignore` excludes sensitive files
- [ ] `render.yaml` present
- [ ] `render-build.sh` present
- [ ] `render-build.sh` is executable (`git update-index --chmod=+x render-build.sh`)
- [ ] Pushed to main branch
- [ ] Repository is accessible (public or Render has access)

---

## During Deployment

### Render Dashboard Setup

- [ ] Signed up/logged into Render.com
- [ ] Created new Blueprint from GitHub
- [ ] Repository connected successfully
- [ ] Both services visible (web + database)
- [ ] Clicked "Apply" to start deployment

### Environment Variables (Auto-configured but verify)

- [ ] `APP_NAME` set
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generated
- [ ] `APP_URL` set to your Render URL
- [ ] `DB_*` variables linked to database
- [ ] `LOG_CHANNEL=stderr`
- [ ] `SESSION_DRIVER=database`
- [ ] `CACHE_STORE=database`
- [ ] `QUEUE_CONNECTION=database`

### Build Process

- [ ] Dependencies installing (watch logs)
- [ ] npm build completing
- [ ] Migrations running
- [ ] Database seeding
- [ ] Config caching
- [ ] "Deploy live" message appears

---

## After Deployment

### Initial Testing

- [ ] App URL accessible
- [ ] Homepage loads without errors
- [ ] Can access login page
- [ ] Can login with default credentials:
  - Email: `admin@example.com`
  - Password: `password123`
- [ ] Dashboard displays correctly
- [ ] Navigation works

### Security

- [ ] Changed admin password
- [ ] Reviewed all user accounts
- [ ] Verified `APP_DEBUG=false`
- [ ] Verified `APP_ENV=production`

### Functionality Testing

- [ ] Projects can be created
- [ ] Projects can be edited/deleted
- [ ] Time entries work
- [ ] User management works
- [ ] Notifications display
- [ ] Analytics page loads

### Email (Optional)

- [ ] Email credentials added to environment
- [ ] Test email sent successfully
- [ ] Notifications arriving

---

## Optional Enhancements

### Domain Configuration

- [ ] Custom domain added in Render
- [ ] DNS records updated
- [ ] SSL certificate active
- [ ] `APP_URL` updated to custom domain

### Scheduled Tasks

- [ ] Cron job created for `schedule:run`
- [ ] Set to run every minute
- [ ] Linked to same database
- [ ] Project due date reminders working

### Queue Worker

- [ ] Background worker created
- [ ] Command: `php artisan queue:work --tries=3`
- [ ] Linked to same database
- [ ] Processing jobs successfully

### Monitoring

- [ ] Reviewed metrics in Render dashboard
- [ ] Set up uptime monitor (optional)
- [ ] Bookmarked logs page

---

## Common Issues Resolution

### Issue: Build Failed

- [ ] Checked build logs for specific error
- [ ] Verified `render-build.sh` is executable
- [ ] Confirmed all dependencies in `composer.json`
- [ ] Manual deploy triggered after fix

### Issue: Database Connection Error

- [ ] Verified database is running
- [ ] Checked DB_* environment variables
- [ ] Confirmed database linked in `render.yaml`
- [ ] Tested connection in Shell: `php artisan db:show`

### Issue: 500 Error on Homepage

- [ ] Verified `APP_KEY` is set
- [ ] Checked logs for error details
- [ ] Cleared config cache: `php artisan config:cache`
- [ ] Verified migrations ran successfully

### Issue: Assets Not Loading

- [ ] Confirmed `npm run build` succeeded in logs
- [ ] Ran `php artisan storage:link`
- [ ] Checked `APP_URL` is correct
- [ ] Verified public directory is accessible

---

## Ongoing Maintenance

### Regular Tasks

- [ ] Monitor application logs weekly
- [ ] Review user accounts monthly
- [ ] Update dependencies quarterly
- [ ] Manual database backup monthly

### Before Each Update

- [ ] Test changes locally
- [ ] Review migration impact
- [ ] Commit and push to GitHub
- [ ] Monitor deployment in Render
- [ ] Test after deployment

---

## Emergency Rollback

If deployment breaks the app:

1. [ ] Go to Render dashboard
2. [ ] Navigate to Events tab
3. [ ] Find last working deployment
4. [ ] Click "Rollback to this version"
5. [ ] Wait for rollback to complete
6. [ ] Verify app is working
7. [ ] Fix issue locally before redeploying

---

## Resources

- **Render Docs**: https://render.com/docs
- **Laravel Docs**: https://laravel.com/docs/12.x
- **Your App Guides**:
  - Detailed: `RENDER_DEPLOYMENT_GUIDE.md`
  - Quick: `QUICK_DEPLOY.md`

---

## Support Contacts

- **Render Support**: https://render.com/support
- **Laravel Community**: https://laravel.io

---

**Last Updated**: 2025-10-29

---

## Notes

Use this space for deployment-specific notes:

```
Deployment Date: ___________
Render URL: ___________
Database Name: ___________
Any custom configurations: ___________
```
