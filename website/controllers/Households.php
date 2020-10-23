<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Households extends CI_Controller {

    private $_page = "households/";

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title'] = "Connexion de ménage";
        $data['active'] = 'households';
        $this->form_validation->set_rules('phone', "Téléphone", 'trim|required|integer|exact_length[8]');

        if ($this->form_validation->run()) {
            $phone = $this->input->post('phone', TRUE);
            $household = $this->households_model->find(array('cellPhone' => $phone));
            if (count($household) > 0) {
                foreach ($household as $h) {
                    $id = $h->idhousehold;
                    $name = $h->household;
                }
                $this->session->set_userdata(array('id' => $id, 'name' => $name));

                redirect('households/complaints');
            } else {
                $this->session->set_flashdata('error', "Le ménage avec ce téléphone n'existe pas");
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            //redirect($this->input->server('HTTP_REFERER'));
        }

        $this->template->load('Sanitations', $this->_page . 'index', $data);
    }

    public function complaints() {
        if (!$this->session->has_userdata('id')) {
            redirect("households");
        }
        $data['title'] = "Palintes";
        $data['active'] = 'households';
        $this->form_validation->set_rules('complaint', "Plainte", 'trim|required');
        $this->form_validation->set_rules('householdId', "Ménage", 'trim|required');

        if ($this->form_validation->run()) {
            $householdId = $this->input->post('householdId', TRUE);
            $complaint = $this->input->post('complaint', TRUE);

            $this->complaints_model->save(array(
                'householdId' => $householdId,
                'complaint' => $complaint,
                'complaintDate' => date('Y-m-d H:i:s')
                    )
            );

            $this->session->set_flashdata('success', "Plainte enrégistrée avec succès");
            redirect('households/complaints');
        } else {
            $this->session->set_flashdata('error', validation_errors());
            //redirect($this->input->server('HTTP_REFERER'));
        }

        $this->template->load('Sanitations', $this->_page . 'complaints', $data);
    }

    public function project() {
        $data['title'] = "Le projet CSSS";
        $data['active'] = 'project';
        $this->template->load('Sanitations', $this->_page . 'project', $data);
    }

    public function sanitation() {
        $data['title'] = "Assainissement";
        $data['active'] = 'sanitation';
        $this->template->load('Sanitations', $this->_page . 'sanitation', $data);
    }

    public function households() {
        $data['title'] = "Ménages";
        $data['active'] = 'households';
        $this->template->load('Sanitations', $this->_page . 'households', $data);
    }

    public function banks() {
        $data['title'] = "Banques";
        $data['active'] = 'banks';
        $this->template->load('Sanitations', $this->_page . 'banks', $data);
    }

    public function contact() {
        $data['title'] = "Nous Contacter";
        $data['active'] = 'contact';
        $this->template->load('Sanitations', $this->_page . 'contact', $data);
    }

}
