# Role-Based Access Control (RBAC) Implementation

## Overview

The project management system now implements proper role-based access control with two user roles:
- **Admin**: Full access to all projects and management features
- **User**: Limited access to assigned projects only

## User Roles

### Admin Role
**Capabilities:**
- ✅ View all projects in the system
- ✅ Create new projects
- ✅ Edit any project
- ✅ Delete any project
- ✅ Assign team members to projects
- ✅ Update project progress
- ✅ View all project statistics

**Access Level:** Full system access

### User Role (Team Members)
**Capabilities:**
- ✅ View only projects they are assigned to
- ✅ View only projects they created
- ✅ Update progress on their assigned projects
- ✅ Mark projects as complete
- ❌ Cannot create new projects
- ❌ Cannot edit project details
- ❌ Cannot delete projects
- ❌ Cannot assign team members

**Access Level:** Limited to assigned projects only

## Implementation Details

### Controller Authorization

#### ProjectController Methods

1. **index()** - List Projects
   - **Admin**: Sees all projects
   - **User**: Sees only assigned projects or projects they created
   - Statistics are filtered based on role

2. **create()** - Create Project Form
   - **Admin**: ✅ Allowed
   - **User**: ❌ Blocked (403 Forbidden)

3. **store()** - Save New Project
   - **Admin**: ✅ Allowed
   - **User**: ❌ Blocked (403 Forbidden)

4. **show()** - View Project Details
   - **Admin**: ✅ Can view any project
   - **User**: ✅ Can view only if assigned or creator

5. **edit()** - Edit Project Form
   - **Admin**: ✅ Allowed for any project
   - **User**: ❌ Blocked (403 Forbidden)

6. **update()** - Save Project Changes
   - **Admin**: ✅ Allowed for any project
   - **User**: ❌ Blocked (403 Forbidden)

7. **destroy()** - Delete Project
   - **Admin**: ✅ Allowed for any project
   - **User**: ❌ Blocked (403 Forbidden)

8. **markComplete()** - Mark Project as Complete
   - **Admin**: ✅ Allowed for any project
   - **User**: ✅ Allowed only for assigned projects

9. **updateProgress()** - Update Project Progress
   - **Admin**: ✅ Allowed for any project
   - **User**: ✅ Allowed only for assigned projects

### View Changes

#### Projects Index (projects/index.blade.php)

**Header Section:**
- **Admin**: Shows "New Project" button
- **User**: Button hidden

**Project List Actions:**
- **Admin**: Shows View, Edit, Delete buttons
- **User**: Shows View and "Mark Complete" buttons (if not already completed)

**Statistics:**
- **Admin**: Shows stats for all projects
- **User**: Shows stats for assigned projects only

### Routes

```php
// All users can access these routes (with authorization checks inside)
Route::resource('projects', ProjectController::class);
Route::post('/projects/{project}/progress', [ProjectController::class, 'updateProgress']);
Route::post('/projects/{project}/mark-complete', [ProjectController::class, 'markComplete']);
```

## Team Member Assignment

### How It Works

1. **Admin creates a project** and assigns team members
2. **Team members** are stored in the `project_user` pivot table
3. **Users** can only see projects where:
   - They are listed in `project_user` table (team members)
   - OR they are the project creator (`created_by` field)

### Database Structure

**projects table:**
- `created_by` - User ID of project creator

**project_user table (pivot):**
- `project_id` - Project ID
- `user_id` - Team member user ID
- `role` - Team member role (member, lead, manager)

## Testing the Implementation

### As Admin

1. Log in as admin (salvesh2004@gmail.com)
2. You should see:
   - All projects in the system
   - "New Project" button
   - Edit and Delete buttons for all projects
3. Create a new project and assign team members
4. Edit any project
5. Delete any project

### As User

1. Log in as regular user (user@mytime.com)
2. You should see:
   - Only projects you're assigned to
   - No "New Project" button
   - Only View and "Mark Complete" buttons
3. Try to access `/projects/create` - Should get 403 Forbidden
4. Try to edit a project - Should get 403 Forbidden
5. Mark a project as complete - Should work
6. Update project progress - Should work

## Security Features

### Authorization Checks

1. **Route-level protection**: All routes require authentication
2. **Controller-level checks**: Each method verifies user permissions
3. **View-level hiding**: Buttons/links hidden based on role
4. **Database-level filtering**: Queries filter data by user access

### Error Handling

- **403 Forbidden**: Returned when user tries unauthorized action
- **Clear error messages**: "Unauthorized action. Only administrators can..."
- **Graceful degradation**: UI adapts to user permissions

## Benefits

1. **Security**: Users can only access their assigned projects
2. **Privacy**: Project data is protected from unauthorized access
3. **Clear Separation**: Admin and user roles have distinct capabilities
4. **Scalability**: Easy to add more roles in the future
5. **User Experience**: Interface adapts to user permissions

## Future Enhancements

Potential improvements:
- Add "Project Manager" role with limited admin capabilities
- Add "Viewer" role for read-only access
- Implement project-level permissions
- Add audit logging for admin actions
- Add bulk assignment of team members
- Add project transfer functionality

## User Management

### Current Users

1. **Salvesh Chand** (Admin)
   - Email: salvesh2004@gmail.com
   - Role: admin
   - Access: Full system access

2. **John Doe** (User)
   - Email: user@mytime.com
   - Role: user
   - Access: Assigned projects only

### Creating New Users

To create a new user, use the database seeder or create manually:

```php
User::create([
    'name' => 'New User',
    'email' => 'newuser@example.com',
    'password' => bcrypt('password'),
    'role' => 'user', // or 'admin'
    'email_notifications' => true,
    'project_updates' => true,
]);
```

## Summary

The system now implements proper role-based access control where:
- **Admins** have full control over all projects
- **Users** can only view and mark complete their assigned projects
- **Team members** are the users assigned to projects
- **Authorization** is enforced at multiple levels for security
- **UI** adapts based on user role for better UX

This ensures data security, proper access control, and a clear separation of responsibilities between administrators and regular users.
