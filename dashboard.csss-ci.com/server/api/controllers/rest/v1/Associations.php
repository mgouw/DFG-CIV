<?php

require 'header.php';

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Associations extends REST_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if (!empty($id)) {
                $data = $this->associations_model->find(array('idassociation' => $id));
            } else {
                $data = $this->associations_model->getAll();
            }
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_post() {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();

            $data = json_decode(file_get_contents('php://input'), true);
            if (!empty($data["association"]) && !empty($data["email"])) {
                $id = uniqid('', true);
                $password = random_string('alnum', 8);
                $email = $this->security->xss_clean($data["email"]);
                $townHallId = $data["townHallId"] ? $this->security->xss_clean($data["townHallId"]) : $userData->id;
                $avec = $this->security->xss_clean($data["association"]);

                $this->associations_model->save(array(
                    'idassociation' => $id,
                    'townHallId' => $townHallId,
                    'association' => $avec,
                    'receipt' => $this->security->xss_clean($data["receipt"]),
                    'district' => $this->security->xss_clean($data["district"]),
                    'email' => $email,
                    'password ' => cryptage($password)
                        )
                );

                $message = "<p>Cher partenaire</p>"
                        . "<p>Votre association <strong>$avec</strong> vient d'être créée.<br>"
                        . "Veuillez trouver ci-dessous vos paramètres de connexion </p>"
                        . "<p>Adresse e-mail : <strong>$email</strong><br>"
                        . "Mot de passe: <strong>$password</strong></p>"
                        . "<p>Pour vous connecter, vous devriez aller à l'adresse suivante : </p>"
                        . "<p><a href='https://dashboard.csss-ci.com/' style='display: inline-block; text-decoration: none; background-color: #ccc; color: #000; font-weight: 700; text-align: center; padding: 15px 25px; border: none; border-radius: 0; margin: 20px;'>https://dashboard.csss-ci.com/</a></p>"
                        . "<p><em>Si vous rencontrez un problème, n'hésitez pas à contacter l'administrateur.</em></p>"
                        . "<p>L'Administrateur</p>";

                $config = array();
                $config['protocol'] = 'mail';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['charset'] = 'utf-8';
                $config['mailtype'] = 'html';
                $config['newline'] = "\r\n";
                $config['wordwrap'] = TRUE;
                $this->email->initialize($config);

                $this->email->clear();
                $this->email->from('no-reply@csss-ci.com', 'CSSS-CARE', 'dashboard@csss-ci.com');
                $this->email->to($email);
                $this->email->subject("CSSS-CARE - Compte Association, Les Paramètres de connexion");
                $this->email->message($message);
                $this->email->send();



                $this->response([$id], REST_Controller::HTTP_OK);
            } else {
                $this->response(['Bad request'], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!empty($data["idassociation"]) && !empty($data["phone"]) && !empty($data["email"])) {
                $userData = $this->authorization_token->userData();

                $id = isset($data["idassociation"]) ? $this->security->xss_clean($data["idassociation"]) : $userData->id;

                $donnee = array(
                    'association' => $this->security->xss_clean($data["association"]),
                    'receipt' => $this->security->xss_clean($data["receipt"]),
                    'district' => $this->security->xss_clean($data["district"]),
                    'phone' => $this->security->xss_clean($data["phone"]),
                    'email' => $this->security->xss_clean($data["email"]),
                );

                $this->associations_model->update($donnee, array('idassociation' => $id));
                $this->response(['Success'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['Bad Request'], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_delete($id) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {

            $this->associations_model->delete(array('idassociation' => $id));
            $this->response(['Success'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function search_get($search) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $data = $this->associations_model->search($search);
            $this->response([$data], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function me_get() {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $data = $this->associations_model->find(array('idassociation' => $userData->id));

            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function password_put() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($data["password"]) && !empty($data["npassword"])) {

            $password = $this->security->xss_clean($data["password"]);
            $npassword = $this->security->xss_clean($data["npassword"]);

            $donnee = array('password' => cryptage($npassword));

            if ($this->verification->authorization()) {
                $verify = $this->verification->authorization();
                $id = $verify->id;

                $this->associations_model->update($donnee, array('idassociation' => $id, 'password ' => cryptage($password)));
                $this->response(['Success'], REST_Controller::HTTP_OK);
            } else {
                $response = ['status' => parent::HTTP_UNAUTHORIZED, 'message' => 'Unauthorized'];
                $this->set_response($response, parent::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(['Bad Request'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function familySize_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->find(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->familySize;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function men_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->men(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->men;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function women_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->women(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->women;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function age0_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->age0(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->age0;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function age15_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->age15(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->age15;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function age22_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->age22(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->age22;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function age49_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            $count = 0;
            $id = isset($id) ? $this->security->xss_clean($id) : $userData->id;
            $families = $this->households_model->age49(array('associationId' => $id));
            foreach ($families as $f) {
                $count += $f->age49;
            }
            $data = isset($count) ? $count : 0;
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function town_get($id = null) {
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $userData = $this->authorization_token->userData();
            if (isset($id)) {
                $id = $this->security->xss_clean($id);
            } else {
                $id = $userData->town;
            }
            $data = $this->associations_model->find(array('townHallId' => $id));
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_options() {
        return $this->response(NULL, REST_Controller::HTTP_OK);
    }

}
