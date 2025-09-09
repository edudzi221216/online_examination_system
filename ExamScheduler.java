import java.sql.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;
import java.util.logging.*;

/**
 * Exam Scheduler - Automated exam management system
 * Replaces the problematic cron-based system with a robust Java application
 */
public class ExamScheduler {
    private static final Logger logger = Logger.getLogger(ExamScheduler.class.getName());
    private static final String DB_URL = "jdbc:mysql://localhost:3306/exam_db";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";
    
    private ScheduledExecutorService scheduler;
    private Connection connection;
    
    public ExamScheduler() {
        setupLogging();
        scheduler = Executors.newScheduledThreadPool(3);
    }
    
    private void setupLogging() {
        try {
            FileHandler fileHandler = new FileHandler("exam_scheduler.log", true);
            fileHandler.setFormatter(new SimpleFormatter());
            logger.addHandler(fileHandler);
            logger.setLevel(Level.ALL);
        } catch (Exception e) {
            System.err.println("Failed to setup logging: " + e.getMessage());
        }
    }
    
    public void start() {
        logger.info("Starting Exam Scheduler...");
        
        try {
            // Test database connection
            testConnection();
            
            // Schedule tasks
            scheduler.scheduleAtFixedRate(this::updateExamStatus, 0, 1, TimeUnit.MINUTES);
            scheduler.scheduleAtFixedRate(this::updateResultPublication, 0, 5, TimeUnit.MINUTES);
            scheduler.scheduleAtFixedRate(this::cleanupOldRecords, 0, 1, TimeUnit.HOURS);
            scheduler.scheduleAtFixedRate(this::autoStartExams, 0, 30, TimeUnit.SECONDS);
            
            logger.info("Exam Scheduler started successfully");
            
            // Keep the main thread alive
            Runtime.getRuntime().addShutdownHook(new Thread(this::shutdown));
            
        } catch (Exception e) {
            logger.severe("Failed to start Exam Scheduler: " + e.getMessage());
            e.printStackTrace();
            System.exit(1);
        }
    }
    
    private void testConnection() throws SQLException {
        connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
        logger.info("Database connection established successfully");
    }
    
    private void autoStartExams() {
        try {
            if (connection == null || connection.isClosed()) {
                connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            }
            
            LocalDateTime now = LocalDateTime.now();
            String currentDate = now.format(DateTimeFormatter.ofPattern("MM/dd/yyyy"));
            String currentTime = now.format(DateTimeFormatter.ofPattern("HH:mm:ss"));
            
            // Start exams that should be active now
            String startSql = "UPDATE tbl_examinations SET status = 'Active' " +
                            "WHERE date = ? AND start_time <= ? AND end_time > ? AND status = 'Inactive'";
            
            try (PreparedStatement stmt = connection.prepareStatement(startSql)) {
                stmt.setString(1, currentDate);
                stmt.setString(2, currentTime);
                stmt.setString(3, currentTime);
                int updated = stmt.executeUpdate();
                if (updated > 0) {
                    logger.info("Auto-started " + updated + " exam(s)");
                }
            }
            
            // Complete exams that should be finished
            String completeSql = "UPDATE tbl_examinations SET status = 'Completed' " +
                               "WHERE ((date < ?) OR (date = ? AND end_time < ?)) AND status = 'Active'";
            
            try (PreparedStatement stmt = connection.prepareStatement(completeSql)) {
                stmt.setString(1, currentDate);
                stmt.setString(2, currentDate);
                stmt.setString(3, currentTime);
                int updated = stmt.executeUpdate();
                if (updated > 0) {
                    logger.info("Auto-completed " + updated + " exam(s)");
                }
            }
            
        } catch (SQLException e) {
            logger.warning("Error in autoStartExams: " + e.getMessage());
        }
    }
    
    private void updateExamStatus() {
        try {
            if (connection == null || connection.isClosed()) {
                connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            }
            
            LocalDateTime now = LocalDateTime.now();
            String currentDate = now.format(DateTimeFormatter.ofPattern("MM/dd/yyyy"));
            String currentTime = now.format(DateTimeFormatter.ofPattern("HH:mm:ss"));
            
            // Update exam status based on current time
            String sql = "UPDATE tbl_examinations SET status = CASE " +
                        "WHEN date < ? THEN 'Completed' " +
                        "WHEN date = ? AND start_time <= ? AND end_time > ? THEN 'Active' " +
                        "WHEN date = ? AND end_time <= ? THEN 'Completed' " +
                        "ELSE 'Inactive' END " +
                        "WHERE status != 'Completed'";
            
            try (PreparedStatement stmt = connection.prepareStatement(sql)) {
                stmt.setString(1, currentDate);
                stmt.setString(2, currentDate);
                stmt.setString(3, currentTime);
                stmt.setString(4, currentTime);
                stmt.setString(5, currentDate);
                stmt.setString(6, currentTime);
                
                int updated = stmt.executeUpdate();
                if (updated > 0) {
                    logger.info("Updated status for " + updated + " exam(s)");
                }
            }
            
        } catch (SQLException e) {
            logger.warning("Error in updateExamStatus: " + e.getMessage());
        }
    }
    
    private void updateResultPublication() {
        try {
            if (connection == null || connection.isClosed()) {
                connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            }
            
            LocalDateTime now = LocalDateTime.now();
            String currentDate = now.format(DateTimeFormatter.ofPattern("yyyy-MM-dd"));
            String currentTime = now.format(DateTimeFormatter.ofPattern("HH:mm:ss"));
            
            // Publish results that are scheduled for now
            String sql = "UPDATE tbl_examinations SET result_publish_status = 'Published' " +
                        "WHERE result_publish_status = 'Scheduled' " +
                        "AND result_publish_start_date <= ? " +
                        "AND result_publish_start_time <= ?";
            
            try (PreparedStatement stmt = connection.prepareStatement(sql)) {
                stmt.setString(1, currentDate);
                stmt.setString(2, currentTime);
                
                int updated = stmt.executeUpdate();
                if (updated > 0) {
                    logger.info("Published results for " + updated + " exam(s)");
                }
            }
            
        } catch (SQLException e) {
            logger.warning("Error in updateResultPublication: " + e.getMessage());
        }
    }
    
    private void cleanupOldRecords() {
        try {
            if (connection == null || connection.isClosed()) {
                connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            }
            
            // Clean up old completed exams (older than 1 year)
            LocalDateTime oneYearAgo = LocalDateTime.now().minusYears(1);
            String cutoffDate = oneYearAgo.format(DateTimeFormatter.ofPattern("MM/dd/yyyy"));
            
            String sql = "DELETE FROM tbl_examinations WHERE status = 'Completed' AND date < ?";
            
            try (PreparedStatement stmt = connection.prepareStatement(sql)) {
                stmt.setString(1, cutoffDate);
                
                int deleted = stmt.executeUpdate();
                if (deleted > 0) {
                    logger.info("Cleaned up " + deleted + " old exam record(s)");
                }
            }
            
        } catch (SQLException e) {
            logger.warning("Error in cleanupOldRecords: " + e.getMessage());
        }
    }
    
    public void shutdown() {
        logger.info("Shutting down Exam Scheduler...");
        scheduler.shutdown();
        try {
            if (!scheduler.awaitTermination(60, TimeUnit.SECONDS)) {
                scheduler.shutdownNow();
            }
        } catch (InterruptedException e) {
            scheduler.shutdownNow();
        }
        
        try {
            if (connection != null && !connection.isClosed()) {
                connection.close();
            }
        } catch (SQLException e) {
            logger.warning("Error closing database connection: " + e.getMessage());
        }
        
        logger.info("Exam Scheduler shutdown complete");
    }
    
    public static void main(String[] args) {
        ExamScheduler scheduler = new ExamScheduler();
        scheduler.start();
        
        // Keep running until interrupted
        try {
            Thread.currentThread().join();
        } catch (InterruptedException e) {
            scheduler.shutdown();
        }
    }
}
