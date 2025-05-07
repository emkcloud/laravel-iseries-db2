<?php

namespace Emkcloud\IseriesDb2\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class IseriesDb2Blueprint extends Blueprint
{
    /**
     * The connection instance.
     */
    protected $systemName = null;

    /**
     * Get collection with table commands
     */
    public function getCommandsCollection(array $names)
    {
        return collect($this->getCommands())->filter(function ($command)
        {
            return in_array($command['name'] ?? '', ['dropColumn', 'renameColumn']);
        });
    }

    /**
     * Get Reply List Entry Commands Automatic.
     */
    public function getReplyListEntryAutomatic()
    {
        return $this->connection->getConfig('replyauto');
    }

    /**
     * Get Reply List Entry Commands Sequence.
     */
    public function getReplyListEntrySequence()
    {
        return $this->connection->getConfig('replyseq');
    }

    /**
     * Get value for system name.
     */
    public function getSystemName()
    {
        return Str::upper($this->systemName);
    }

    /**
     * Specify a system name for the table.
     */
    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;
    }

    /**
     * Get the raw SQL statements for the blueprint.
     */
    public function toSql()
    {
        $this->ReplyListEntrySetup();

        return parent::toSql();
    }

    /**
     * Add Reply List Entry Commands (CPA32B2).
     */
    public function ReplyListEntrySetup()
    {
        if ($this->getCommandsCollection(['dropColumn', 'renameColumn'])->isNotEmpty())
        {
            if ($this->getReplyListEntryAutomatic() && ctype_digit($this->getReplyListEntrySequence()))
            {
                array_unshift($this->commands,
                    $this->createCommand('ReplyListEntryAdd'),
                    $this->createCommand('ReplyListEntryJob'));

                array_push($this->commands,
                    $this->createCommand('ReplyListEntryRemove'));
            }
        }
    }
}
