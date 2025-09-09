<?php
/**
 * Script to fix remaining button patterns
 */

// Function to update specific button patterns
function updateSpecificButtons($content) {
    // Pattern 1: View Exams buttons in classresults.php
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="results.php?cn='.$row['name'].'">View Exams</a></button></td>',
        '<td><a href="results.php?cn='.$row['name'].'" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Exams</a></td>',
        $content
    );
    
    // Pattern 2: Publish buttons
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="pages/pub.php?cn='.$row['name'].'">Publish</a></button>',
        '<td><a href="pages/pub.php?cn='.$row['name'].'" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Publish</a>',
        $content
    );
    
    // Pattern 3: Unpublish buttons
    $content = str_replace(
        '<button type="button" class="btn btn-danger" aria-expanded="false"><a\'; ?> <?php print \' href="pages/unpub.php?cn='.$row['name'].'">Unpublish</a></button></td>',
        '<a href="pages/unpub.php?cn='.$row['name'].'" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Unpublish</a></td>',
        $content
    );
    
    // Pattern 4: View Status buttons
    $content = str_replace(
        '<td><button type="button" class="btn btn-default" aria-expanded="false"><a\'; ?> <?php print \' href="resultstats.php?cn='.$row['name'].'">View Status</a></button></td>',
        '<td><a href="resultstats.php?cn='.$row['name'].'" class="btn btn-default btn-sm"><i class="fa fa-info"></i> View Status</a></td>',
        $content
    );
    
    // Pattern 5: View Exams buttons in classexamination.php
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="examinations.php?cn='.$row['name'].'">View Exams</a></button></td>',
        '<td><a href="examinations.php?cn='.$row['name'].'" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Exams</a></td>',
        $content
    );
    
    // Pattern 6: Block buttons
    $content = str_replace(
        '<td><button type="button" class="btn btn-danger" aria-expanded="false"><a\'; ?> <?php print \' href="pages/disable.php?sd='.$row['user_id'].'">Block</a></button>',
        '<td><a href="pages/disable.php?sd='.$row['user_id'].'" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Block</a>',
        $content
    );
    
    // Pattern 7: Unblock buttons
    $content = str_replace(
        '<button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="pages/enable.php?sd='.$row['user_id'].'">Unblock</a></button></td>',
        '<a href="pages/enable.php?sd='.$row['user_id'].'" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Unblock</a></td>',
        $content
    );
    
    // Pattern 8: Paid buttons
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="pages/paid.php?sd='.$row['user_id'].'">Paid</a></button>',
        '<td><a href="pages/paid.php?sd='.$row['user_id'].'" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Paid</a>',
        $content
    );
    
    // Pattern 9: Unpaid buttons
    $content = str_replace(
        '<button type="button" class="btn btn-danger" aria-expanded="false"><a\'; ?> <?php print \' href="pages/unpaid.php?sd='.$row['user_id'].'">Unpaid</a></button></td>',
        '<a href="pages/unpaid.php?sd='.$row['user_id'].'" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Unpaid</a></td>',
        $content
    );
    
    // Pattern 10: View Student buttons (accountant)
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="vstudents.php?cn='.$row['name'].'">View Student</a></button></td>',
        '<td><a href="vstudents.php?cn='.$row['name'].'" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Student</a></td>',
        $content
    );
    
    // Pattern 11: View Student buttons (blockclassstd)
    $content = str_replace(
        '<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; ?> <?php print \' href="blockstd.php?cn='.$row['name'].'">View Student</a></button></td>',
        '<td><a href="blockstd.php?cn='.$row['name'].'" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Student</a></td>',
        $content
    );
    
    // Pattern 12: Delete buttons (classes)
    $content = str_replace(
        '<button type="button" class="btn btn-default" aria-expanded="false"><a\'; ?> <?php print \' href="pages/drop_cl.php?id='.$row['class_id'].'">Delete</a></button>',
        '<a href="pages/drop_cl.php?id='.$row['class_id'].'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 13: Delete buttons (academic year)
    $content = str_replace(
        '<button type="button" class="btn btn-default" aria-expanded="false"><a\'; ?> <?php print \' href="pages/drop_ayr.php?id='.$row['ay_id'].'">Delete</a></button>',
        '<a href="pages/drop_ayr.php?id='.$row['ay_id'].'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 14: Delete buttons (subject)
    $content = str_replace(
        '<button type="button" class="btn btn-default" aria-expanded="false"><a\'; ?> <?php print \' href="pages/drop_sb.php?id='.$row['subject_id'].'">Delete</a></button>',
        '<a href="pages/drop_sb.php?id='.$row['subject_id'].'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 15: Delete Notice buttons
    $content = str_replace(
        '<button type="button" class="btn btn-primary" aria-expanded="false"><a\';?> onclick = "return confirm(\' Are You Sure You Want To Delete the Notice ?\')" <?php print \' href="pages/drop_notice.php?id='.$row['notice'].'">Delete</a></button>',
        '<a onclick="return confirm(\'Are You Sure You Want To Delete the Notice?\')" href="pages/drop_notice.php?id='.$row['notice'].'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    return $content;
}

// Files to update
$files_to_update = [
    'teacher/classresults.php',
    'teacher/classexamination.php',
    'admin/classexamination.php',
    'admin/classes.php',
    'admin/blockclassstd.php',
    'admin/blockstd.php',
    'admin/acad.php',
    'admin/notice.php',
    'admin/subject.php',
    'accountant/vstudents.php',
    'accountant/students.php'
];

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original_content = $content;
        
        // Update button patterns
        $content = updateSpecificButtons($content);
        
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

echo "\n=== Final Button Fix Complete ===\n";
echo "All remaining buttons have been updated to use the new styling.\n";
?> 