<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class SessionClass extends MySQL implements SessionHandlerInterface{

    public function __construct(){
        parent::__construct();

        session_set_save_handler(array($this, 'open'),
                                 array($this, 'close'),
                                 array($this, 'read'),
                                 array($this, 'write'),
                                 array($this, 'destroy'),
                                 array($this, 'gc'));
        // The following prevents unexpected effects when using objects as save handlers.
        register_shutdown_function('session_write_close');

        /*$this->connection;
        // Set handler to overide SESSION  
        session_set_save_handler(  
            array($this, "open"),  
            array($this, "close"),  
            array($this, "read"),  
            array($this, "write"),  
            array($this, "destroy"),  
            array($this, "gc")  
        );
        
        session_set_save_handler('open','close','read','write','destroy','clean');
        register_shutdown_function('session_write_close');
        // Start the session  
        session_start();*/
    }

    /*** Open ***/  
    public function open($savepath, $id){  
        $data='';
        print "OPEN eleje Session opened.\n";
        print "Sess_path: $savepath\n";
        print "id: $id\n\n";
        print "OPEN vege. ";
        return true;
        // If successful
        //delete old session handlers
        //$limit = time() - (3600 * 24);
        $stmt = $this->connection ->prepare("SELECT data FROM sessions WHERE id = ? LIMIT 1");
        $stmt -> bind_param('s',$id);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($data);
        $stmt -> fetch();
        $data=$data;
//$stmt->selectRowsFoundCounter() == 1
        if($data !== '' ){
            // Return True
            return true;
        }
        // Return False
        return false;
    }

    /*** Close ***/  
    public function close(){  
        print "Session closed.\n";
        // Close the database connection  
        // If successful  
        if($this->connection->close()){  
            // Return True  
            return true;  
        }  
        // Return False  
        return false;  
    }  

    /*** Read ***/  
    public function read($id){  
        print "Session read.\n";
        print "Sess_ID: $id\n";
        return '';
        // Set query  
        $stmt = $this->connection ->prepare('SELECT data FROM sessions WHERE id = ? LIMIT 1');
    
        // Bind the Id  
        $stmt -> bind_param('s',$id);
    
        // Attempt execution  
        // If successful  
        if($stmt ->execute()){  
            // Save returned row  
            //$row = $stmt ->single();  
            $result = $stmt->get_result();
            $num = $result->num_rows;
            // Return the data  
            //return $row['data'];  
            if ($num>0) {
                $record = $result->fetch_assoc();
                return $record['data'];
            }
        }else{  
            // Return an empty string  
            return '';  
        }
        
    }

    /*** Write ***/  
    public function write($id, $data){  
        
        print "Session value written.\n";
        print "Sess_ID: $id\n";
        print "Data: $data\n\n";

        // Create time stamp  
        $access = time();
        
        // Set query  
        $stmt = $this->connection ->prepare('REPLACE INTO sessions (id,access,data) VALUES (?, ?, ?)');
        
        // Bind data  
        $stmt -> bind_param('sis',$id,$access,$data);
        
        // Attempt Execution  
        // If successful  
        if($stmt -> execute()){  
            // Return True  
            return true;  
        }
        
        // Return False  
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