<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreatePaymentsTable
 * 
 * Cria a tabela de pagamentos para integração com gateways.
 */
class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'appointment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'gateway' => [
                'type'       => 'ENUM',
                'constraint' => ['mercadopago', 'stripe', 'pagseguro', 'pix', 'manual'],
                'default'    => 'mercadopago',
            ],
            'gateway_payment_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'ID do pagamento no gateway',
            ],
            'gateway_preference_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'ID da preferência/checkout no gateway',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Valor do pagamento',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'BRL',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'approved', 'rejected', 'cancelled', 'refunded', 'expired'],
                'default'    => 'pending',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Método: credit_card, debit_card, pix, boleto',
            ],
            'installments' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
                'comment'    => 'Número de parcelas',
            ],
            'payer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'payer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'payer_document' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'CPF do pagador',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'pix_qr_code' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'QR Code PIX base64',
            ],
            'pix_qr_code_base64' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'QR Code PIX imagem base64',
            ],
            'pix_copy_paste' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Código copia e cola do PIX',
            ],
            'boleto_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'boleto_barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'gateway_response' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Resposta completa do gateway',
            ],
            'webhook_received_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Expiração do pagamento (PIX/Boleto)',
            ],
            'refunded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'refund_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('tenant_id');
        $this->forge->addKey('appointment_id');
        $this->forge->addKey('client_id');
        $this->forge->addKey('status');
        $this->forge->addKey('gateway_payment_id');
        $this->forge->addKey(['gateway', 'status']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
