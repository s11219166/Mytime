# Real-Time Project Updates - Code Examples & Reference

## API Endpoint Examples

### 1. Get Projects Updates

**Request:**
```bash
GET /api/projects/updates?status=active&search=website&last_update=1704067200
Authorization: Bearer YOUR_TOKEN
X-CSRF-TOKEN: YOUR_CSRF_TOKEN
```

**Response (Success):**
```json
{
  "success": true,
  "projects": [
    {
      "id": 1,
      "name": "Website Redesign",
      "description": "Complete website redesign project",
      "status": "active",
      "priority": "high",
      "progress": 75,
      "budget": 5000.00,
      "start_date": "Jan 01, 2025",
      "end_date": "Dec 31, 2025",
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "team_members": [
        {
          "id": 2,
          "name": "Jane Smith"
        },
        {
          "id": 3,
          "name": "Bob Johnson"
        }
      ],
      "updated_at": 1704067200
    },
    {
      "id": 2,
      "name": "Mobile App",
      "description": "Mobile application development",
      "status": "active",
      "priority": "medium",
      "progress": 50,
      "budget": 8000.00,
      "start_date": "Feb 01, 2025",
      "end_date": "Aug 31, 2025",
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "team_members": [
        {
          "id": 4,
          "name": "Alice Brown"
        }
      ],
      "updated_at": 1704067200
    }
  ],
  "timestamp": 1704067200
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Unauthorized",
  "timestamp": 1704067200
}
```

### 2. Get Projects Statistics

**Request:**
```bash
GET /api/projects/stats?status=all
Authorization: Bearer YOUR_TOKEN
X-CSRF-TOKEN: YOUR_CSRF_TOKEN
```

**Response (Success - Admin):**
```json
{
  "success": true,
  "stats": {
    "total": 10,
    "active": 5,
    "pending": 2,
    "completed": 2,
    "overdue": 1
  },
  "timestamp": 1704067200
}
```

**Response (Success - Regular User):**
```json
{
  "success": true,
  "stats": {
    "total": 5,
    "active": 3,
    "pending": 1,
    "completed": 1
  },
  "timestamp": 1704067200
}
```

## JavaScript Code Examples

### 1. Initialize Real-Time Updates

```javascript
// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeRealTimeUpdates();
});

function initializeRealTimeUpdates() {
    // Check for updates every 3 seconds
    updateCheckInterval = setInterval(checkForProjectUpdates, 3000);
    
    // Also check on page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page became visible, check for updates immediately
            checkForProjectUpdates();
        }
    });
}
```

### 2. Check for Project Updates

```javascript
function checkForProjectUpdates() {
    if (isUpdating) return; // Prevent concurrent requests
    
    isUpdating = true;
    
    // Get current filter values
    const params = new URLSearchParams({
        status: document.querySelector('select[name="status"]')?.value || 'all',
        search: document.querySelector('input[name="search"]')?.value || '',
        last_update: lastUpdateTimestamp
    });
    
    // Fetch updated projects
    fetch(`/api/projects/updates?${params}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.projects) {
            updateProjectsDisplay(data.projects);
            lastUpdateTimestamp = data.timestamp;
        }
    })
    .catch(error => console.error('Error checking for updates:', error))
    .finally(() => {
        isUpdating = false;
    });
    
    // Also check for stats updates
    checkForStatsUpdates();
}
```

### 3. Update Projects Display

```javascript
function updateProjectsDisplay(projects) {
    // Get current project IDs from DOM
    const currentProjectIds = Array.from(document.querySelectorAll('[data-project-id]'))
        .map(el => parseInt(el.getAttribute('data-project-id')));
    
    // Get new project IDs from API
    const newProjectIds = projects.map(p => p.id);
    
    // Check if projects list has changed
    const hasChanges = currentProjectIds.length !== newProjectIds.length ||
                      !currentProjectIds.every(id => newProjectIds.includes(id)) ||
                      !newProjectIds.every(id => currentProjectIds.includes(id));
    
    if (hasChanges) {
        // Reload the page to show new/removed projects
        location.reload();
        return;
    }
    
    // Update individual project rows
    projects.forEach(project => {
        const projectRow = document.querySelector(`[data-project-id="${project.id}"]`);
        if (projectRow) {
            updateProjectRow(projectRow, project);
        }
    });
}
```

### 4. Update Individual Project Row

```javascript
function updateProjectRow(row, projectData) {
    // Update status badge
    const statusBadge = row.querySelector('[data-field="status"]');
    if (statusBadge) {
        const statusColors = {
            'active': 'success',
            'inprogress': 'primary',
            'review_pending': 'warning',
            'completed': 'success',
            'cancelled': 'dark',
        };
        const color = statusColors[projectData.status] || 'secondary';
        statusBadge.className = `badge bg-${color}`;
        statusBadge.textContent = projectData.status
            .replace('_', ' ')
            .charAt(0).toUpperCase() + 
            projectData.status.replace('_', ' ').slice(1);
    }
    
    // Update priority badge
    const priorityBadge = row.querySelector('[data-field="priority"]');
    if (priorityBadge) {
        const priorityColors = {
            'urgent': 'danger',
            'high': 'warning',
            'medium': 'info',
            'low': 'secondary'
        };
        const color = priorityColors[projectData.priority] || 'secondary';
        priorityBadge.className = `badge bg-${color}`;
        priorityBadge.innerHTML = `<i class="fas fa-flag me-1"></i>${projectData.priority.charAt(0).toUpperCase() + projectData.priority.slice(1)}`;
    }
    
    // Update progress bar
    const progressBar = row.querySelector('[data-field="progress"]');
    if (progressBar) {
        const progressDiv = progressBar.querySelector('.progress-bar');
        if (progressDiv) {
            progressDiv.style.width = projectData.progress + '%';
        }
        const progressText = progressBar.querySelector('small');
        if (progressText) {
            progressText.textContent = projectData.progress + '%';
        }
    }
    
    // Update budget
    const budgetCell = row.querySelector('[data-field="budget"]');
    if (budgetCell && projectData.budget) {
        budgetCell.innerHTML = `<span class="text-success fw-semibold">$${parseFloat(projectData.budget).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
    }
}
```

### 5. Update Statistics Display

```javascript
function updateStatsDisplay(stats) {
    // Update total projects stat
    const totalStat = document.querySelector('[data-stat="total"]');
    if (totalStat) {
        totalStat.textContent = stats.total;
    }
    
    // Update active projects stat
    const activeStat = document.querySelector('[data-stat="active"]');
    if (activeStat) {
        activeStat.textContent = stats.active;
    }
    
    // Update pending projects stat
    const pendingStat = document.querySelector('[data-stat="pending"]');
    if (pendingStat) {
        pendingStat.textContent = stats.pending;
    }
    
    // Update completed projects stat
    const completedStat = document.querySelector('[data-stat="completed"]');
    if (completedStat) {
        completedStat.textContent = stats.completed;
    }
    
    // Update overdue projects stat (if admin)
    const overdueStat = document.querySelector('[data-stat="overdue"]');
    if (overdueStat && stats.overdue !== undefined) {
        overdueStat.textContent = stats.overdue;
    }
}
```

## PHP/Laravel Code Examples

### 1. Get Updates Method

```php
public function getUpdates(Request $request)
{
    $user = Auth::user();
    
    // Build base query
    $baseQuery = \Illuminate\Support\Facades\DB::table('projects')
        ->select('projects.*');
    
    // Role-based filtering
    if ($user->isUser()) {
        $baseQuery->where(function($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereIn('id', function($subQuery) use ($user) {
                  $subQuery->select('project_id')
                           ->from('project_user')
                           ->where('user_id', $user->id);
              });
        });
    }
    
    // Filter by status if provided
    if ($request->filled('status') && $request->status !== 'all') {
        $baseQuery->where('status', $request->status);
    }

    // Search functionality
    if ($request->filled('search')) {
        $baseQuery->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    // Get project IDs
    $projectIds = $baseQuery->pluck('id')->toArray();
    
    // Load projects with relationships
    $projects = Project::with(['creator', 'teamMembers'])
        ->whereIn('id', $projectIds)
        ->orderByRaw("CASE 
            WHEN status = 'completed' THEN 2
            WHEN status = 'cancelled' THEN 2
            ELSE 1
        END ASC")
        ->orderByRaw("CASE 
            WHEN end_date IS NULL THEN 1
            ELSE 0
        END ASC")
        ->orderBy('end_date', 'ASC')
        ->latest('updated_at')
        ->get();

    // Format projects for response
    $formattedProjects = $projects->map(function ($project) {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => $project->status,
            'priority' => $project->priority,
            'progress' => $project->progress,
            'budget' => $project->budget,
            'start_date' => $project->start_date->format('M d, Y'),
            'end_date' => $project->end_date ? $project->end_date->format('M d, Y') : null,
            'creator' => $project->creator ? [
                'id' => $project->creator->id,
                'name' => $project->creator->name,
            ] : null,
            'team_members' => $project->teamMembers->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                ];
            })->toArray(),
            'updated_at' => $project->updated_at->timestamp,
        ];
    });

    return response()->json([
        'success' => true,
        'projects' => $formattedProjects,
        'timestamp' => now()->timestamp,
    ]);
}
```

### 2. Get Stats Method

```php
public function getStats(Request $request)
{
    $user = Auth::user();

    if ($user->isAdmin()) {
        $stats = [
            'total' => \Illuminate\Support\Facades\DB::table('projects')->count(),
            'active' => \Illuminate\Support\Facades\DB::table('projects')
                ->whereIn('status', ['active', 'inprogress'])->count(),
            'pending' => \Illuminate\Support\Facades\DB::table('projects')
                ->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
            'completed' => \Illuminate\Support\Facades\DB::table('projects')
                ->where('status', 'completed')->count(),
            'overdue' => \Illuminate\Support\Facades\DB::table('projects')
                ->where('end_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
        ];
    } else {
        // Stats for regular users (only their projects)
        $userProjectIds = \Illuminate\Support\Facades\DB::table('projects')
            ->where('created_by', $user->id)
            ->orWhereIn('id', function($q) use ($user) {
                $q->select('project_id')
                  ->from('project_user')
                  ->where('user_id', $user->id);
            })
            ->pluck('id');

        $stats = [
            'total' => $userProjectIds->count(),
            'active' => \Illuminate\Support\Facades\DB::table('projects')
                ->whereIn('id', $userProjectIds)
                ->whereIn('status', ['active', 'inprogress'])->count(),
            'pending' => \Illuminate\Support\Facades\DB::table('projects')
                ->whereIn('id', $userProjectIds)
                ->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
            'completed' => \Illuminate\Support\Facades\DB::table('projects')
                ->whereIn('id', $userProjectIds)
                ->where('status', 'completed')->count(),
        ];
    }

    return response()->json([
        'success' => true,
        'stats' => $stats,
        'timestamp' => now()->timestamp,
    ]);
}
```

## HTML/Blade Template Examples

### 1. Statistics Card with Data Attribute

```blade
<div class="col-xl col-lg-4 col-md-6">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="total">
                        {{ $stats['total'] }}
                    </h3>
                    <p class="mb-0 text-muted small">Total Projects</p>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 2. Project Table Row with Data Attributes

```blade
<tr data-project-id="{{ $project->id }}">
    <td>
        <div class="d-flex align-items-center">
            <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); flex-shrink: 0;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="ms-3">
                <h6 class="mb-1">{{ $project->name }}</h6>
                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
            </div>
        </div>
    </td>
    <td data-field="status">
        <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
        </span>
    </td>
    <td data-field="priority">
        <span class="badge bg-{{ $priorityColors[$project->priority] ?? 'secondary' }}">
            <i class="fas fa-flag me-1"></i>{{ ucfirst($project->priority) }}
        </span>
    </td>
    <td data-field="progress">
        <div class="d-flex align-items-center gap-2">
            <div class="progress" style="width: 100px; height: 20px;">
                <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
            </div>
            <small>{{ $project->progress }}%</small>
        </div>
    </td>
    <td data-field="budget">
        @if($project->budget)
            <span class="text-success fw-semibold">${{ number_format($project->budget, 2) }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
</tr>
```

## Testing Examples

### 1. Test API Endpoint with cURL

```bash
# Get projects updates
curl -X GET "http://localhost/api/projects/updates?status=active" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Accept: application/json"

# Get projects stats
curl -X GET "http://localhost/api/projects/stats" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Accept: application/json"
```

### 2. Test with JavaScript Fetch

```javascript
// Test API endpoint
async function testAPI() {
    try {
        const response = await fetch('/api/projects/updates', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        console.log('API Response:', data);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Call the test function
testAPI();
```

## Debugging Tips

### 1. Check API Response

```javascript
// In browser console
fetch('/api/projects/updates')
    .then(r => r.json())
    .then(d => console.log(d));
```

### 2. Monitor Updates

```javascript
// Add logging to update function
function checkForProjectUpdates() {
    console.log('Checking for updates at', new Date().toLocaleTimeString());
    // ... rest of function
}
```

### 3. Check DOM Elements

```javascript
// In browser console
document.querySelectorAll('[data-project-id]').length; // Count projects
document.querySelector('[data-stat="total"]').textContent; // Get total stat
```

## Conclusion

These code examples provide a complete reference for understanding and working with the real-time updates system. Use them as a guide for customization or troubleshooting.
