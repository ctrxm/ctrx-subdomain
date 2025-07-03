<?php

namespace App\Models;

use CodeIgniter\Model;

class SubdomainModel extends Model
{
    protected $table            = 'subdomains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'domain_id', 'cloudflare_record_id', 'name', 'type', 'content', 'proxied'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil semua subdomain milik seorang user,
     * sekaligus mengambil nama domain dasarnya.
     */
    public function getSubdomainsByUser(int $userId)
    {
        return $this->select('subdomains.*, domains.domain_name')
                    ->join('domains', 'domains.id = subdomains.domain_id')
                    ->where('subdomains.user_id', $userId)
                    ->orderBy('subdomains.created_at', 'DESC')
                    ->findAll();
    }
}
