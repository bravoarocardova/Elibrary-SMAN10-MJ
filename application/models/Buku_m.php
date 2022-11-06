<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Buku_m extends CI_Model
{

    public function getBuku($kd_buku = null)
    {
        $this->db->join('kategori', 'kategori.id_kategori = buku.id_kategori');
        $this->db->order_by('judul_buku', 'ASC');
        if ($kd_buku != null) {
            $this->db->where('kd_buku', $kd_buku);
        }
        return $this->db->get('buku')->result();
    }

    public function getBukuByName($name)
    {
        $this->db->join('kategori', 'kategori.id_kategori = buku.id_kategori');
        $this->db->order_by('judul_buku', 'ASC');
        $this->db->like('judul_buku', $name,'both');
        return $this->db->get('buku')->result();
    }

    public function getBukuByKodeBukuNoResult($kd_buku)
    {
        $this->db->join('kategori', 'kategori.id_kategori = buku.id_kategori');
        $this->db->order_by('judul_buku', 'ASC');
        $this->db->where('kd_buku', $kd_buku);
        return $this->db->get('buku');
    }

    public function getBukuByNoPinjam($no_pinjam)
    {
        $this->db->join('kategori', 'kategori.id_kategori = buku.id_kategori');
        $this->db->join('peminjaman', 'peminjaman.kd_buku = buku.kd_buku');
        $this->db->order_by('judul_buku', 'ASC');
        $this->db->where('no_pinjam', $no_pinjam);
        return $this->db->get('buku')->result();
    }

    public function simpanBuku($post)
    {
        $data = [
            'kd_buku' => $post['kd_buku'],
            'judul_buku' => $post['judul_buku'],
            'pengarang' => $post['pengarang'],
            'tahun_terbit' => $post['tahun_terbit'],
            'penerbit' => $post['penerbit'],
            'id_kategori' => $post['id_kategori'],
            'isbn' => $post['isbn'],
            'jumlah_buku' => $post['jumlah_buku'],
            'cover' => $post['cover'],
            'file' => $post['file'],
        ];

        return $this->db->insert('buku',$data);
    }

    public function hapusBuku($kd_buku)
    {
        $buku = $this->getBuku($kd_buku)[0];
        if (file_exists('./assets/buku/cover/'. $buku->cover)) 
        {
            unlink('./assets/buku/cover/'. $buku->cover);
        }
        if (file_exists('./assets/buku/file/'. $buku->file)) 
        {
            unlink('./assets/buku/file/'. $buku->file);
        }
        return $this->db->delete('buku', ['kd_buku' => $kd_buku]);
    }

    public function getKategori()
    {
        return $this->db->get('kategori')->result();
    }

    public function simpanKategori($post)
    {
        $data = [
            'nama_kategori' => $post['nama_kategori'],
        ];

        return $this->db->insert('kategori',$data);
    }

    public function hapusKategori($id)
    {
        return $this->db->delete('kategori', ['id_kategori' => $id]);
    }

    public function getDenda()
    {
        return $this->db->get('biaya_denda')->result();
    }

    public function simpanDenda($post)
    {
        $data = [
            'harga_denda' => $post['harga_denda'],
            'stat' => empty($this->db->get('biaya_denda')->result()) ? 'Aktif' : 'Tidak Aktif',
            'tgl_tetap' => date('Y-m-d')
        ];

        return $this->db->insert('biaya_denda',$data);
    }

    public function setActiveDenda($id)
    {
        $this->db->update('biaya_denda',[ 'stat' => 'Tidak Aktif']);
        
        return $this->db->update('biaya_denda',[ 'stat' => 'Aktif'], ['id_biaya_denda' => $id]);
    }

    public function hapusDenda($id)
    {
        return $this->db->delete('biaya_denda', ['id_biaya_denda' => $id]);
    }

}



