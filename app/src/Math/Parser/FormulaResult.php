<?php

namespace Math\Parser;

class FormulaResult
{
    /** @var FormulaResultEntry[] */
    private $entries;

    /**
     * FormulaResult constructor.
     * @param FormulaResultEntry[] $entries
     */
    public function __construct(...$entries)
    {
        $this->entries = $entries;
    }

    public function __toString()
    {
        $string = "";
        foreach ($this->entries as $entry) {
            if ($string) $string .= "\n";
            if ($entry->isDbz()) {
                $string .= "Division by zero in: " ;
                if ($entry->getVariable()) $string .= $entry->getVariable() . " = ";
                $string .= $entry->getOriginal();
            } else {
                $string .= $entry->getVariable() ? $entry->getVariable() : $entry->getOriginal();
                $string .= " = " . (string) $entry->getResult();
            }
        }
        return $string;
    }

    public function addResult(FormulaResultEntry $entry)
    {
        if ($entry->getVariable()) {
            $this->entries[$entry->getVariable()] = $entry;
        } else {
            $this->entries[] = $entry;
        }
    }

    /**
     * @return FormulaResultEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}