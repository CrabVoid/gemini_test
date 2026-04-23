<?php
require_once __DIR__ . '/../src/Models/Customer.php';

// Ieslēdzam PHP kļūdu ziņošanu tikai gadījumam, ja kaut kas noiet greizi izstrādes laikā
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =========================================================================
// SECTION: Action Dispatcher
// Purpose: Decide which operation to perform (Create, Delete, or List).
// =========================================================================

// Mainīgie kļūdu un ziņojumu glabāšanai, kurus rādīt skatā
$error = '';
$success = '';

/**
 * SUB-SECTION: Handle Form Submission (POST Requests)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Apstrādājam DELETE darbību
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        if (CustomerModel::delete($id)) {
            header("Location: /customers.php?success=Customer+deleted");
            exit;
        }
    } 
    
    // 2. Apstrādājam jaunā klienta izveidi (CREATE)
    else {
        // Iegūstam un attīrām (trim) datus no formas
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $points    = (int)($_POST['points'] ?? 0);

        // a) Validācija: Pārbaudām, vai lauki nav tukši
        if (empty($firstname) || empty($lastname) || empty($email)) {
            $error = "Visi lauki ir obligāti (Vārds, Uzvārds, E-pasts).";
        } 
        // b) Validācija: Pārbaudām e-pasta formātu
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Lūdzu, ievadiet derīgu e-pasta adresi.";
        } 
        // c) Darbība: Ja viss kārtībā, mēģinām saglabāt
        else {
            $data = [
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
                'points'    => $points
            ];

            if (CustomerModel::create($data)) {
                // Pēc veiksmīgas izveides veicam pāradresāciju (Redirect), 
                // lai pēc lapas pārlādes (F5) dati neiesūtītos vēlreiz.
                header("Location: /customers.php?success=Customer+added+successfully");
                exit;
            } else {
                $error = "Neizdevās saglabāt klientu. Mēģiniet vēlreiz.";
            }
        }
    }
}

/**
 * SUB-SECTION: Load View Data
 */
// Iegūstam visus klientus saraksta rādīšanai
$customers = CustomerModel::all();

// Iegūstam panākumu ziņojumu no URL, ja tāds ir (pēc Redirect)
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// =========================================================================
// SECTION: Load View Template
// Purpose: Send the prepared data to the visual HTML file.
// =========================================================================
require_once __DIR__ . '/../src/views/customers.php';
?>