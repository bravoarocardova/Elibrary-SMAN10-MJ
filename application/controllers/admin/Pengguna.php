<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengguna extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_admin_not_login();

        $this->load->model('pengguna_m');
        // $this->load->model('transaksi_m');
    }

    public function index()
    {

        $data = [
            'user' => $this->pengguna_m->getUser()
        ];
        
        $this->template->load('template/template','pengguna/pengguna',$data);
    }

    private function msgSuccess($msg)
    {
        return '<div class="alert alert-success alert-dismissible text-white" role="alert">
                    <span class="text-sm">'.$msg.'</span>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                        aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    }

    private function msgError($msg)
    {
        return '<div class="alert alert-danger alert-dismissible text-white" role="alert">
                    <span class="text-sm">'.$msg.'</span>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                        aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    }

    public function user_proses()
    {

        $post = $this->input->post(NULL, TRUE);
        if (isset($post['simpan'])) {
            if ($post['password'] == $post['konfirmasi_password']) {
                if ($this->pengguna_m->simpanUser($post)) {
                    $this->session->set_flashdata('msg', $this->msgSuccess('Berhasil ditambahkan'));
                    redirect('admin/pengguna');
                }else{
                    $this->session->set_flashdata('msg', $this->msgError('Gagal ditambahkan username telah ada!'));
                    redirect('admin/pengguna');
                }
            }else{
                $this->session->set_flashdata('msg', $this->msgError('Gagal ditambahkan Konfirmasi Password tidak cocok!'));
                redirect('admin/pengguna');
            }
        }else{
            redirect('admin/pengguna');
        }
    }

    public function user_hapus($id = null)
    {

        if ($id != null) {
            if ($this->pengguna_m->hapusUser($id)) {
                $this->session->set_flashdata('msg', $this->msgSuccess('Berhasil dihapus'));
                redirect('admin/pengguna');
            }else{
                $this->session->set_flashdata('msg', $this->msgError('Gagal dihapus!'));
            redirect('admin/pengguna');
            }
        }else{
            redirect('admin/pengguna');
        }
    }

    public function active($id)
    {

        if ($this->pengguna_m->setActive($id)) {
            redirect('admin/pengguna');
        }
        redirect('admin/pengguna');
    }

    public function profil()
    {

        $data = [
            'user' => $this->pengguna_m->getUser($this->session->userdata('id_user'))[0],
            // 'peminjaman' => $this->transaksi_m->getPeminjamanDistinctUser($this->session->userdata('id_user')),
            // 'pengembalian' => $this->transaksi_m->getPengembalianDistinctUser($this->session->userdata('id_user')),
        ];

        $this->template->load('template/template','pengguna/profil',$data);
    }

    public function lihat_profil($id = null)
    {

        if ($id != null) {
            $data = [
                'user' => $this->pengguna_m->getUser($id)[0],
                // 'peminjaman' => $this->transaksi_m->getPeminjamanDistinctUser($id),
                // 'pengembalian' => $this->transaksi_m->getPengembalianDistinctUser($id),
            ];
    
            $this->template->load('template/template','pengguna/profil',$data);
        }else{
            redirect('admin/pengguna');
        }
    }

    public function update_profil()
    {

        $post = $this->input->post(NULL, TRUE);
        if (isset($post['simpan'])) {
            $config['upload_path'] = './assets/img/profil/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 4096;
            $config['file_name'] = 'profil-'.date('ymd').time();
            $this->load->library('upload', $config);

            $post['foto'] = null;
            if(@$_FILES['foto']['error'] == 0){
                $user = $this->pengguna_m->getUser($post['id'])[0];
                
                if ($this->upload->do_upload('foto')) {
                    $post['foto'] = $this->upload->data('file_name');
                    if (file_exists('./assets/img/profil/'. $user->foto)) 
                    {
                        unlink('./assets/img/profil/'. $user->foto);
                    }
                }
            }

            if ($this->pengguna_m->updateProfil($post)) {
                $this->session->set_flashdata('msg', $this->msgSuccess('Berhasil ubah'));
            }else{
                $this->session->set_flashdata('msg', $this->msgError('Gagal diubah username telah ada!'));
            }
            if (is_admin()) {
                redirect('admin/pengguna/lihat_profil/'.$post['id']);
            }else{
                redirect('admin/pengguna/profil/');
            }
        }else{
            if (is_admin()) {
                redirect('admin/pengguna/');
            }else{
                redirect('admin/pengguna/profil/');
            };
        }
    }
    
    public function update_password()
    {

        $post = $this->input->post(NULL, TRUE);
        if (isset($post['simpan'])) {
            if ($this->pengguna_m->updatePassword($post)) {
                $this->session->set_flashdata('msg', $this->msgSuccess('Berhasil diubah'));
            }else{
                $this->session->set_flashdata('msg', $this->msgError('Gagal diubah'));
            }
            if (is_admin()) {
                redirect('admin/pengguna/lihat_profil/'.$post['id']);
            }else{
                redirect('admin/pengguna/profil/');
            }
        }else{
            redirect('admin/pengguna/profil/'.$post['id']);
        }
    }
}