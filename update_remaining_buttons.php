<?php
/**
 * Script to update all remaining buttons to use the new styling
 */

// Function to update button patterns
function updateButtonPatterns($content) {
    // Pattern 1: Button with nested anchor tags
    $content = preg_replace(
        '/<button type="button" class="btn btn-([^"]+)"[^>]*><a[^>]*href="([^"]+)"[^>]*>([^<]+)<\/a><\/button>/',
        '<a href="$2" class="btn btn-$1 btn-sm"><i class="fa fa-eye"></i> $3</a>',
        $content
    );
    
    // Pattern 2: Button with nested anchor tags (alternative format)
    $content = preg_replace(
        '/<button type="button" class="btn btn-([^"]+)"[^>]*><a[^>]*onclick[^>]*href="([^"]+)"[^>]*>([^<]+)<\/a><\/button>/',
        '<a onclick="return confirm(\'Are you sure?\')" href="$2" class="btn btn-$1 btn-sm"><i class="fa fa-check"></i> $3</a>',
        $content
    );
    
    // Pattern 3: Submit buttons without icons
    $content = preg_replace(
        '/<button type="submit" class="btn btn-primary">Submit<\/button>/',
        '<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>',
        $content
    );
    
    // Pattern 4: Change Password buttons
    $content = preg_replace(
        '/<button type="submit" class="btn btn-primary">Change Password<\/button>/',
        '<button type="submit" class="btn btn-primary"><i class="fa fa-key"></i> Change Password</button>',
        $content
    );
    
    // Pattern 5: Update buttons
    $content = preg_replace(
        '/<button type="submit" class="btn btn-primary">Update ([^<]+)<\/button>/',
        '<button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> Update $1</button>',
        $content
    );
    
    // Pattern 6: Publish buttons
    $content = preg_replace(
        '/<button type="submit" class="btn btn-primary">Publish<\/button>/',
        '<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Publish</button>',
        $content
    );
    
    // Pattern 7: Delete buttons with btn-youtube class
    $content = preg_replace(
        '/class="btn btn-youtube m-b-xs"/',
        'class="btn btn-danger btn-sm"',
        $content
    );
    
    // Pattern 8: View Student buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-success"[^>]*><a[^>]*href="vstudents\.php\?cn=([^"]+)"[^>]*>View Student<\/a><\/button>/',
        '<a href="vstudents.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Student</a>',
        $content
    );
    
    // Pattern 9: View Exams buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-success"[^>]*><a[^>]*href="examinations\.php\?cn=([^"]+)"[^>]*>View Exams<\/a><\/button>/',
        '<a href="examinations.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Exams</a>',
        $content
    );
    
    // Pattern 10: View Results buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-success"[^>]*><a[^>]*href="view-results\.php\?eid=([^"]+)"[^>]*>View Results<\/a><\/button>/',
        '<a href="view-results.php?eid=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Results</a>',
        $content
    );
    
    // Pattern 11: Block/Unblock buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-danger"[^>]*><a[^>]*href="pages\/disable\.php\?sd=([^"]+)"[^>]*>Block<\/a><\/button>/',
        '<a href="pages/disable.php?sd=$1" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Block</a>',
        $content
    );
    
    $content = preg_replace(
        '/<button type="button" class="btn btn-success"[^>]*><a[^>]*href="pages\/enable\.php\?sd=([^"]+)"[^>]*>Unblock<\/a><\/button>/',
        '<a href="pages/enable.php?sd=$1" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Unblock</a>',
        $content
    );
    
    // Pattern 12: Paid/Unpaid buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-success"[^>]*><a[^>]*href="pages\/paid\.php\?sd=([^"]+)"[^>]*>Paid<\/a><\/button>/',
        '<a href="pages/paid.php?sd=$1" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Paid</a>',
        $content
    );
    
    $content = preg_replace(
        '/<button type="button" class="btn btn-danger"[^>]*><a[^>]*href="pages\/unpaid\.php\?sd=([^"]+)"[^>]*>Unpaid<\/a><\/button>/',
        '<a href="pages/unpaid.php?sd=$1" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Unpaid</a>',
        $content
    );
    
    // Pattern 13: Delete buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-default"[^>]*><a[^>]*href="pages\/drop_[^"]+\.php\?id=([^"]+)"[^>]*>Delete<\/a><\/button>/',
        '<a href="pages/drop_sb.php?id=$1" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    return $content;
}

// Files to update
$files_to_update = [
    'teacher/view-results.php',
    'teacher/vstudents.php',
    'teacher/view-questions.php',
    'teacher/subject.php',
    'teacher/results.php',
    'teacher/questions.php',
    'teacher/students.php',
    'teacher/profile.php',
    'teacher/notice.php',
    'teacher/edit-student.php',
    'teacher/edit-exam.php',
    'teacher/classexamination.php',
    'teacher/add-questions.php',
    'teacher/classresults.php',
    'student/profile.php',
    'admin/blockclassstd.php',
    'admin/classes.php',
    'admin/edit-teacher.php',
    'admin/results.php',
    'admin/teacher.php',
    'admin/view-questions.php',
    'admin/vstudents.php',
    'admin/view-results.php',
    'admin/subject.php',
    'admin/profile.php',
    'admin/notice.php',
    'admin/questions.php',
    'admin/classexamination.php',
    'admin/blockstd.php',
    'admin/add-questions.php',
    'admin/accountant.php',
    'admin/acad.php',
    'accountant/students.php',
    'accountant/vstudents.php',
    'accountant/profile.php'
];

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original_content = $content;
        
        // Update button patterns
        $content = updateButtonPatterns($content);
        
        // Write back if changes were made
        if ($content !== $original_content) {
            if (file_put_contents($file, $content)) {
                echo "✓ Updated $file\n";
            } else {
                echo "✗ Failed to update $file\n";
            }
        } else {
            echo "✓ No changes needed for $file\n";
        }
    } else {
        echo "✗ File not found: $file\n";
    }
}

echo "\n=== Button Update Complete ===\n";
echo "All remaining buttons have been updated to use the new styling.\n";
?> 