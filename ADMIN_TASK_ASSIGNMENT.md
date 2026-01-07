# Admin-Only Task Assignment Feature

## Overview
This feature allows **only administrators** to assign tasks to other users. Regular users can only create tasks for themselves.

## Implementation Details

### 1. Role System
- Added `role` column to `users` table (enum: 'user', 'admin')
- Default role is 'user'
- User model includes `isAdmin()` and `isUser()` helper methods

### 2. Admin Capabilities

#### Task Management
- ✅ **View All Tasks**: Admins can see all tasks from all users
- ✅ **Assign Tasks**: Admins can assign tasks to any user via dropdown in create form
- ✅ **Update Any Task**: Admins can update status of any task
- ✅ **Delete Any Task**: Admins can delete any task

#### User Management
- ✅ **Set User Roles**: Admins can set role (user/admin) when creating users
- ✅ **View All Users**: Admins can see all users with role badges

### 3. Regular User Capabilities
- ✅ **View Own Tasks**: Regular users can only see their own tasks
- ✅ **Create Tasks**: Regular users can only create tasks for themselves
- ✅ **Update Own Tasks**: Regular users can only update their own tasks
- ✅ **Delete Own Tasks**: Regular users can only delete their own tasks

## Database Changes

### Migration: `add_role_to_users_table`
```php
$table->enum('role', ['user', 'admin'])->default('user')->after('email');
```

## Code Changes

### Models
- **User Model**: Added `role` to fillable, added `isAdmin()` and `isUser()` methods

### Controllers
- **Web\TaskController**: 
  - `index()`: Shows all tasks for admins, own tasks for regular users
  - `create()`: Passes users list to view (only for admins)
  - `store()`: Allows admins to assign to any user
  - `updateStatus()`: Admins can update any task
  - `destroy()`: Admins can delete any task

- **API\TaskController**: Same authorization logic applied

- **Web\UserController**: 
  - `store()`: Admins can set role when creating users

### Views
- **tasks/create.blade.php**: Shows "Assign To User" dropdown (admin only)
- **tasks/index.blade.php**: Shows "Assigned To" column (admin only)
- **users/create.blade.php**: Shows "Role" dropdown (admin only)
- **users/index.blade.php**: Shows admin badge for admin users

## Usage

### For Admins

1. **Creating a Task for Another User**:
   - Go to "Create Task"
   - Fill in task details
   - Select a user from "Assign To User" dropdown
   - Click "Create Task"

2. **Viewing All Tasks**:
   - Go to "Tasks" page
   - See all tasks from all users
   - "Assigned To" column shows who each task belongs to

3. **Creating Admin Users**:
   - Go to "Create User"
   - Fill in user details
   - Select "Admin" from Role dropdown
   - Click "Create User"

### For Regular Users

1. **Creating a Task**:
   - Go to "Create Task"
   - Fill in task details
   - No "Assign To" dropdown visible
   - Task is automatically assigned to you

2. **Viewing Tasks**:
   - Go to "Tasks" page
   - Only see your own tasks
   - No "Assigned To" column visible

## Security

- ✅ Authorization checks in all controllers
- ✅ Role-based access control
- ✅ Regular users cannot access other users' tasks
- ✅ Regular users cannot assign tasks to others
- ✅ API endpoints also respect role-based access

## Testing

### Test Admin Features:
1. Login as admin (rajesh.kumar@example.com / password)
2. Create a task and assign it to another user
3. Verify you can see all tasks
4. Verify you can update/delete any task

### Test Regular User Features:
1. Login as regular user (priya.sharma@example.com / password)
2. Create a task (should be auto-assigned to you)
3. Verify you only see your own tasks
4. Verify you cannot access other users' tasks

## Default Admin User

- **Email**: rajesh.kumar@example.com
- **Password**: password
- **Role**: admin

---

*Last Updated: January 7, 2026*

