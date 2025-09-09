<?php
/**
 * Final script to update all remaining buttons to use the new styling
 */

// Function to update button patterns
function updateButtonPatterns($content) {
    // Pattern 1: View Student buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="vstudents\.php\?cn=([^"]+)"\>View Student\<\/a\><\/button><\/td>/',
        '<td><a href="vstudents.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Student</a></td>',
        $content
    );
    
    // Pattern 2: View Exams buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="examinations\.php\?cn=([^"]+)"\>View Exams\<\/a\><\/button><\/td>/',
        '<td><a href="examinations.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Exams</a></td>',
        $content
    );
    
    // Pattern 3: View Results buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="view-results\.php\?eid=([^"]+)"\>View Results\<\/a\><\/button><\/td>/',
        '<td><a href="view-results.php?eid=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Results</a></td>',
        $content
    );
    
    // Pattern 4: View Student buttons (blockclassstd)
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="blockstd\.php\?cn=([^"]+)"\>View Student\<\/a\><\/button><\/td>/',
        '<td><a href="blockstd.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View Student</a></td>',
        $content
    );
    
    // Pattern 5: Publish buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/pub\.php\?cn=([^"]+)"\>Publish\<\/a\><\/button>/',
        '<td><a href="pages/pub.php?cn=$1" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Publish</a>',
        $content
    );
    
    // Pattern 6: Unpublish buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-danger" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/unpub\.php\?cn=([^"]+)"\>Unpublish\<\/a\><\/button><\/td>/',
        '<a href="pages/unpub.php?cn=$1" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Unpublish</a></td>',
        $content
    );
    
    // Pattern 7: View Status buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-default" aria-expanded="false"><a\'; \?\> \<?php print \' href="resultstats\.php\?cn=([^"]+)"\>View Status\<\/a\><\/button><\/td>/',
        '<td><a href="resultstats.php?cn=$1" class="btn btn-default btn-sm"><i class="fa fa-info"></i> View Status</a></td>',
        $content
    );
    
    // Pattern 8: Block buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-danger" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/disable\.php\?sd=([^"]+)"\>Block\<\/a\><\/button>/',
        '<td><a href="pages/disable.php?sd=$1" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Block</a>',
        $content
    );
    
    // Pattern 9: Unblock buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/enable\.php\?sd=([^"]+)"\>Unblock\<\/a\><\/button><\/td>/',
        '<a href="pages/enable.php?sd=$1" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Unblock</a></td>',
        $content
    );
    
    // Pattern 10: Paid buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/paid\.php\?sd=([^"]+)"\>Paid\<\/a\><\/button>/',
        '<td><a href="pages/paid.php?sd=$1" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Paid</a>',
        $content
    );
    
    // Pattern 11: Unpaid buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-danger" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/unpaid\.php\?sd=([^"]+)"\>Unpaid\<\/a\><\/button><\/td>/',
        '<a href="pages/unpaid.php?sd=$1" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Unpaid</a></td>',
        $content
    );
    
    // Pattern 12: Delete buttons (subject)
    $content = preg_replace(
        '/<button type="button" class="btn btn-default" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/drop_sb\.php\?id=([^"]+)"\>Delete\<\/a\><\/button>/',
        '<a href="pages/drop_sb.php?id=$1" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 13: Delete buttons (class)
    $content = preg_replace(
        '/<button type="button" class="btn btn-default" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/drop_cl\.php\?id=([^"]+)"\>Delete\<\/a\><\/button>/',
        '<a href="pages/drop_cl.php?id=$1" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 14: Delete buttons (academic year)
    $content = preg_replace(
        '/<button type="button" class="btn btn-default" aria-expanded="false"><a\'; \?\> \<?php print \' href="pages\/drop_ayr\.php\?id=([^"]+)"\>Delete\<\/a\><\/button>/',
        '<a href="pages/drop_ayr.php?id=$1" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>',
        $content
    );
    
    // Pattern 15: Delete Notice buttons
    $content = preg_replace(
        '/<button type="button" class="btn btn-primary" aria-expanded="false"><a\';?\> onclick = "return confirm\(\' Are You Sure You Want To Delete the Notice \?\'\)" \<?php print \' href="pages\/drop_notice\.php\?id=([^"]+)"\>Delete/',
        '<a onclick="return confirm(\'Are You Sure You Want To Delete the Notice?\')" href="pages/drop_notice.php?id=$1" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete',
        $content
    );
    
    // Pattern 16: Reactivate buttons
    $content = preg_replace(
        '/<td><button type="button" class="btn btn-success" aria-expanded="false"><a\';?\> onclick = "return confirm\(\'Reactivate exam for \<?php echo \$row\[\'student_name\'\]\;\ \?\> \?\'\)" \<?php print \' href="pages\/re-activate\.php\?rid=([^"]+)"\>Re-Exam\<\/a\><\/button><\/td>/',
        '<td><a onclick="return confirm(\'Reactivate exam for <?php echo $row[\'student_name\']; ?>?\')" href="pages/re-activate.php?rid=$1" class="btn btn-success btn-sm"><i class="fa fa-refresh"></i> Re-Exam</a></td>',
        $content
    );
    
    return $content;
}

// Files to update
$files_to_update = [
    'teacher/students.php',
    'teacher/results.php',
    'teacher/subject.php',
    'teacher/notice.php',
    'teacher/classresults.php',
    'teacher/classexamination.php',
    'admin/view-results.php',
    'admin/results.php',
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

echo "\n=== Final Button Update Complete ===\n";
echo "All remaining buttons have been updated to use the new styling.\n";
?> 