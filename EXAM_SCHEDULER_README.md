# Exam Scheduling System

This document explains the new exam scheduling system that automatically manages exam availability based on date and time.

## Features Added

### 1. Start Time and End Time Fields
- Added `start_time` and `end_time` fields to the `tbl_examinations` table
- These fields allow administrators to set specific time windows for exams
- Default times are 09:00:00 (9 AM) for start time and 17:00:00 (5 PM) for end time

### 2. Automatic Exam Availability Management
- Exams are automatically activated when the current date and time fall within the scheduled window
- Exams are automatically deactivated when the time window has passed
- Real-time availability checking for students

### 3. Enhanced Student Interface
- Students can see exam start and end times
- Clear indication of exam availability status
- Upcoming exams section showing future scheduled exams
- Better user experience with time-based access control

## Database Changes

### New Fields Added to `tbl_examinations`:
```sql
ALTER TABLE `tbl_examinations` 
ADD COLUMN `start_time` TIME DEFAULT '09:00:00' AFTER `date`,
ADD COLUMN `end_time` TIME DEFAULT '17:00:00' AFTER `start_time`;
```

## Files Modified/Created

### Modified Files:
1. **admin/examinations.php**
   - Added start time and end time input fields to the exam creation form
   - Updated the exam listing table to display start and end times
   - Enhanced the admin interface to show time information

2. **admin/pages/add_exam.php**
   - Updated to handle start_time and end_time form submissions
   - Modified the database insert query to include the new time fields

3. **student/index.php**
   - Integrated the exam scheduler functionality
   - Added real-time availability checking
   - Enhanced the exam display with time information
   - Added upcoming exams section

### New Files Created:
1. **includes/exam_scheduler.php**
   - Core functionality for exam availability management
   - Functions for checking exam availability
   - Functions for updating exam statuses
   - Helper functions for formatting dates and times

2. **cron/update_exam_status.php**
   - Cron job script for automatic exam status updates
   - Logging functionality for tracking updates
   - Can be scheduled to run periodically (recommended: every 5 minutes)

3. **database/add_time_fields.sql**
   - SQL script to add the new time fields to existing databases

## Setup Instructions

### 1. Database Setup
Run the SQL script to add the new fields:
```bash
mysql -u your_username -p your_database < database/add_time_fields.sql
```

### 2. Cron Job Setup (Optional but Recommended)
Set up a cron job to automatically update exam statuses:

```bash
# Edit crontab
crontab -e

# Add this line to run every 5 minutes
*/5 * * * * php /path/to/your/project/cron/update_exam_status.php
```

### 3. File Permissions
Ensure the logs directory is writable:
```bash
mkdir -p logs
chmod 755 logs
```

## Usage

### For Administrators:
1. **Creating Exams**: When creating a new exam, you can now specify:
   - Exam date
   - Start time (when the exam becomes available)
   - End time (when the exam becomes unavailable)

2. **Managing Exams**: The system will automatically:
   - Activate exams when the current time falls within the scheduled window
   - Deactivate exams when the time window has passed

### For Students:
1. **Viewing Available Exams**: Students will see:
   - Currently available exams with start/end times
   - Clear indication of exam availability
   - Upcoming exams that are scheduled but not yet available

2. **Taking Exams**: Students can only access exams that are:
   - Currently within the scheduled time window
   - Marked as active by the system

## Functions Available

### Core Functions (in exam_scheduler.php):

1. **checkExamAvailability($exam_id, $conn)**
   - Checks if a specific exam is currently available
   - Returns array with availability status and message

2. **updateExamStatus($conn)**
   - Automatically updates all exam statuses based on current date/time
   - Should be called periodically or via cron job

3. **getActiveExams($class, $conn)**
   - Returns all currently active exams for a specific class
   - Filters by current date and time

4. **getUpcomingExams($class, $conn)**
   - Returns upcoming exams for a specific class
   - Shows exams that are scheduled but not yet available

5. **getExamStatus($exam_id, $conn)**
   - Returns detailed status information for a specific exam
   - Includes availability, current date/time, and formatted information

6. **formatTime($time)**
   - Formats time for display (e.g., "09:00:00" → "9:00 AM")

7. **formatDate($date)**
   - Formats date for display (e.g., "07/08/2023" → "July 8, 2023")

## Timezone Configuration

The system uses Africa/Dar_es_salaam timezone. To change this, modify the timezone setting in:
- `includes/exam_scheduler.php`
- `cron/update_exam_status.php`

## Logging

The cron job creates logs in `logs/exam_status_updates.log` with:
- Timestamp of each update
- Number of active exams
- Recent status changes
- Any errors that occur

## Troubleshooting

### Common Issues:

1. **Exams not showing as available**
   - Check that the current date matches the exam date
   - Verify that current time is between start_time and end_time
   - Ensure the exam status is 'Active'

2. **Cron job not running**
   - Check file permissions
   - Verify the path to the PHP executable
   - Check the logs for error messages

3. **Time zone issues**
   - Ensure the server timezone is set correctly
   - Update the timezone setting in the scheduler files

## Security Considerations

- All database queries use prepared statements to prevent SQL injection
- Input validation is performed on all form submissions
- Time-based access control prevents unauthorized exam access
- Logging provides audit trail for exam status changes

## Future Enhancements

Potential improvements for the future:
- Email notifications when exams become available/unavailable
- More granular time controls (e.g., specific days of the week)
- Integration with calendar systems
- Mobile app notifications
- Advanced scheduling features (recurring exams, etc.) 