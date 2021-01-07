<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class SessionClass extends MySQL implements SessionHandlerInterface{

    public function __construct(){
        parent::__construct();
        // Set handler to overide SESSION
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
        register_shutdown_function('session_write_close');
    }

    /*** Open ***/
    /**
     * Executed when the session is started automatically, or
     * manually with session_start();
     *
     * @param string $savePath
     * @param string $sessionId
     * @return boolean
     */
    public function open($savePath, $sessionId){  
        if($this->connection){
            return true;
        }
        return false;
    }

    /*** Close ***/  
    public function close(){  
        if($this->connection->close()){
        @session_write_close();
        return true;
    }
    return false;   
    }  

    /*** Read ***/  
    public function read($sessionId){  
        print "Session read.\n";
        print "Sess_ID: $sessionId\n";
        //return '';
        // Set query  
        $stmt = $this->connection ->prepare('SELECT data FROM sessions WHERE id = ? LIMIT 1');
    
        // Bind the Id  
        $stmt -> bind_param('s',$sessionId);
    
        // Attempt execution  
        // If successful  
        if($stmt ->execute()){
            // Save returned row  
            //$row = $stmt ->single();  
            $result = $stmt->get_result();
            $num = $result->num_rows;
            // Return the data  
            //return $row['data'];  
            
            print "Read eredmeny:$num \n";
            if ($num>0) {
                $record = $result->fetch_assoc();
                print "Read result eredmeny:".$record['data']."\n";
                return $record['data'];
            }
        }else{  
            // Return an empty string  
            return '';  
        }
        
    }

    /*** Write ***/  
    /**
     * Used to save the session and close.
     * close() is called after this function executes.
     *
     * @param string $sessionId Id of the current session
     * @param string $sessionData serialized session data
     */
    public function write($sessionId, $data){  
        
        print "Session value written.\n";
        print "Sess_ID: $sessionId\n";
        print "Data: $data\n\n";

        // Create time stamp  
        $access = time();
        print "WRITE Access: $access\n\n";
        $stmt = $this->connection->prepare("SELECT data FROM sessions WHERE id = ?");
        // Bind the Id  
        $stmt -> bind_param('s',$sessionId);

        if($stmt ->execute()){
            $update = $this->connection->prepare('UPDATE sessions SET data = ? AND access = ? WHERE id = ?');
            $update -> bind_param('sis', $data,$access,$sessionId);
            if($update -> execute()){            
                return true;
            }
        }else{
            print "INSERT a tablaba!!";
            // Set query  
            $stmt = $this->connection ->prepare('INSERT INTO sessions (id,access,data) VALUES (?, ?, ?)');
            
            // Bind data  
            $stmt -> bind_param('sis', $sessionId,$access,$data);
            
            // Attempt Execution  
            // If successful  
            if($stmt -> execute()){  
                return true;  
            }
        }

        return false;
    }

    /*** Destroy ***/  
    public function destroy($id){  
        
        print "Session destroy called.\n";
        // Set query  
        $stmt = $this->connection ->prepare('DELETE FROM sessions WHERE id = ?');
        
        // Bind data  
        $stmt->bind_param('s', $id);
        
        // Attempt execution  
        // If successful  
        if($stmt->execute()){  
            // Return True  
            return true;  
        }
        
        // Return False  
        return false;
    }

     /*** Garbage Collection ***/  
    public function gc($max){  
        
        print "Session garbage collection called.\n";
        print "Sess_maxlifetime: $max\n";
        // Calculate what is to be deemed old
        $old = time() - $max;
    
        // Set query  
        $stmt = $this->connection ->prepare('DELETE * FROM sessions WHERE access < ?');
    
        // Bind data  
        $stmt->bind_param('i', $old);
    
        // Attempt execution  
        if($stmt->execute()){  
            // Return True  
            return true;  
        }
    
        // Return False  
        return false;
    } 

}
?>