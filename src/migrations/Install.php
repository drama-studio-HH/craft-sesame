<?php

namespace thedrama\craftsesame\migrations;

use craft\db\Migration;
use thedrama\craftsesame\helpers\Table;

class Install extends Migration
{

    // these tables should be cleaned up when this plugin is uninstalled
    const TABLES = [
        Table::AUTHENTICATION,
        Table::SETTINGS,
    ];

    public function safeUp(): bool
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropForeignKeys();
        $this->removeTables();

        return true;
    }

    public function createTables(): void
    {
        $this->createTable(Table::AUTHENTICATION, [
            'id' => $this->primaryKey(),
            'token' => $this->string(32)->notNull(),
            'userId' => $this->integer()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'lifetime' => $this->integer()->notNull(),
            'tokenUsed' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        $this->createTable(Table::SETTINGS, [
            'id' => $this->primaryKey(),
            'siteId' => $this->integer()->notNull(),
            'allowedHosts' => $this->string(),
            'logoSource' => $this->json()->notNull(),
            'lifetime' => $this->integer()->notNull()->defaultValue(15 * 60),
            'redirectUrl' => $this->string(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
        ]);
    }

    public function createIndexes(): void
    {
        $this->createIndex(null, Table::AUTHENTICATION, 'token', false);
    }

    public function addForeignKeys(): void
    {
        $this->addForeignKey(null, Table::AUTHENTICATION, 'userId', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::SETTINGS, 'siteId', '{{%sites}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function dropForeignKeys(): void
    {
        foreach ($this::TABLES as $table) {
            if ($this->db->tableExists($table)) {
                $this->dropAllForeignKeysToTable($table);
            }
        }
    }

    public function removeTables(): void
    {
        foreach ($this::TABLES as $table) {
            if ($this->db->tableExists($table)) {
                $this->dropTable($table);
            }
        }
    }
}