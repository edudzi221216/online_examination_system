@echo off
echo ========================================
echo    EMS Exam Scheduler Launcher
echo ========================================
echo.

REM Check if Java is installed
echo Checking Java installation...
java -version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Java is not installed or not in PATH
    echo Please install Java 8 or higher and try again
    echo.
    echo Download Java from: https://adoptium.net/
    pause
    exit /b 1
)

echo Java found ✓
echo.

REM Check if MySQL JDBC driver exists
if not exist "mysql-connector-java-8.0.33.jar" (
    echo Downloading MySQL JDBC driver...
    powershell -Command "Invoke-WebRequest -Uri 'https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.33/mysql-connector-java-8.0.33.jar' -OutFile 'mysql-connector-java-8.0.33.jar'"
    if errorlevel 1 (
        echo ERROR: Failed to download MySQL JDBC driver
        echo Please check your internet connection and try again
        pause
        exit /b 1
    )
    echo MySQL JDBC driver downloaded ✓
) else (
    echo MySQL JDBC driver found ✓
)

echo.

REM Compile the Java program
echo Compiling ExamScheduler.java...
javac -cp "mysql-connector-java-8.0.33.jar" ExamScheduler.java
if errorlevel 1 (
    echo ERROR: Compilation failed
    echo Please check the Java code for syntax errors
    pause
    exit /b 1
)
echo Compilation successful ✓

echo.
echo ========================================
echo Starting Exam Scheduler...
echo ========================================
echo.
echo The scheduler will:
echo - Auto-start exams at scheduled times
echo - Update exam status every minute
echo - Publish results every 5 minutes
echo - Clean up old records every hour
echo.
echo Press Ctrl+C to stop the scheduler
echo.

REM Run the scheduler
java -cp ".;mysql-connector-java-8.0.33.jar" ExamScheduler

echo.
echo Exam Scheduler stopped.
pause
