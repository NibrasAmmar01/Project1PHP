<?php
include 'Connexion.ini.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];

        
        $search_query = "SELECT IdLogin, Pseudo, Mot_passe FROM Login WHERE Mail = ?";
        $stmt = $conn->prepare($search_query);

        
        if ($stmt === false) {
            die('Error in prepare statement: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);

        
        if ($stmt->error) {
            die('Error in bind_param: ' . $stmt->error);
        }

        
        if (!$stmt->execute()) {
            die('Error in execute statement: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row["IdLogin"];
            $storedPassword = $row["Mot_passe"];

           
            if ($_POST['password'] === $storedPassword) {
                

                $insert_query = "INSERT INTO Login_Stat (IdLogin, dateheure_connexion) VALUES (?, NOW())"; // Fixed the INSERT query
                $stmtInsert = $conn->prepare($insert_query);

                
                if ($stmtInsert === false) {
                    die('Error in prepare statement (insert): ' . $conn->error);
                }

                $stmtInsert->bind_param("i", $userId);

                
                if ($stmtInsert->error) {
                    die('Error in bind_param (insert): ' . $stmtInsert->error);
                }

                
                if (!$stmtInsert->execute()) {
                    die('Error in execute statement (insert): ' . $stmtInsert->error);
                }

              
                $total_connections_query = "SELECT COUNT(*) AS total_connections FROM Login_Stat WHERE DATE(dateheure_connexion) = CURDATE()";
                $result = $conn->query($total_connections_query);
                $total_connections = $result->fetch_assoc()['total_connections'];

                
                echo "<table border='1'>
                        <tr>
                            <th>IdLogin</th>
                            <th>Dateheure Connexion</th>
                        </tr>";

                
                $login_stat_query = "SELECT IdLogin, dateheure_connexion FROM Login_Stat WHERE DATE(dateheure_connexion) = CURDATE()";
                $result = $conn->query($login_stat_query);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['IdLogin']}</td>
                            <td>{$row['dateheure_connexion']}</td>
                          </tr>";
                }

                
                echo "<tr>
                        <td colspan='2'>Nombre total de connexions aujourd'hui: $total_connections</td>
                      </tr></table>";

            } else {
                
                echo "Mot de passe incorrect. Retour à la page d'accueil.";
            }

        } else {
            
            echo "Utilisateur non trouvé. Retour à la page d'accueil.";
        }

        
        $stmtInsert->close();

        
        $stmt->close();

    } else {
        
        echo "Veuillez fournir une adresse e-mail et un mot de passe.";
    }
}


$conn->close();
?>
