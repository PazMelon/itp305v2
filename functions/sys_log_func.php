<?php
require_once __DIR__ . '/../includes/config.php';

class SysLogFunction
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

   public function logSystemEvent($name, $description, $creator_id) {
            try {
            $stmt = $this->conn->prepare("INSERT INTO sys_logs (name, description, creator) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $description, $creator_id);
            $stmt->execute();
            
            // File logging
            $logMessage = sprintf(
                "[%s] System Event: %s - %s (Created by: %d)\n",
                date('Y-m-d H:i:s'),
                $name,
                $description,
                $creator_id
            );
            
            $this->logToFile($logMessage);
            
            return true;
        } catch (mysqli_sql_exception $e) {
            $errorMessage = sprintf(
                "[%s] ERROR: %s\n",
                date('Y-m-d H:i:s'),
                $e->getMessage()
            );
            $this->logToFile($errorMessage);
            return false;
        }
    }

    public function logToFile($message) {

        $logDirectory = __DIR__ . '/../logs';
        $logFile = $logDirectory . '/log.txt';
        
        // Create logs directory if it doesn't exist
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0640 , true);
        }
        
        // Append the message to the log file
        file_put_contents($logFile, $message, FILE_APPEND);
    }
}

// Initialize SysLogFunction
$syslog = new SysLogFunction($conn);

?>