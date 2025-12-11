<?php

namespace Shuchkin;

class SimpleXLSX {
    private $workbook;
    public $sheetNames = [];
    private $sheets = [];
    private $sharedStrings = [];
    private $styles = [];

    public static function parse($filepath) {
        $xlsx = new self();
        return $xlsx->parseFile($filepath) ? $xlsx : false;
    }

    public static function parseError() {
        return self::$error;
    }

    private static $error = false;

    private function parseFile($filepath) {
        self::$error = false;

        if ( ! file_exists($filepath)) {
            self::$error = "File not found.";
            return false;
        }

        $zip = new \ZipArchive();
        if ( $zip->open($filepath) !== true ) {
            self::$error = "Failed to open file as ZIP.";
            return false;
        }

        // -------------------
        // Load shared strings
        // -------------------
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text = (string)$si->t;
                } elseif (isset($si->r)) {
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                }
                $this->sharedStrings[] = $text;
            }
        }

        // -------------------
        // Load sheet names
        // -------------------
        if (($index = $zip->locateName('xl/workbook.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->sheets->sheet as $sheet) {
                $this->sheetNames[] = (string)$sheet['name'];
            }
        }

        // -------------------
        // Load individual sheets
        // -------------------
        foreach ($this->sheetNames as $i => $name) {
            $sheetFile = "xl/worksheets/sheet".($i+1).".xml";
            if (($index = $zip->locateName($sheetFile)) !== false) {
                $xml = simplexml_load_string($zip->getFromIndex($index));
                $this->sheets[] = $xml;
            }
        }

        $zip->close();
        return true;
    }

    public function rows($sheetIndex = 0) {
        if (!isset($this->sheets[$sheetIndex])) return [];

        $rows = [];
        foreach ($this->sheets[$sheetIndex]->sheetData->row as $row) {
            $r = [];
            foreach ($row->c as $c) {
                $value = null;
                $type = (string)$c['t'];
                $v = (string)$c->v;

                if ($type === 's') {
                    $value = $this->sharedStrings[(int)$v] ?? null;
                } else {
                    $value = $v;
                }

                $col = $this->cellColumn((string)$c['r']);
                $r[$col] = $value;
            }
            ksort($r);
            $rows[] = array_values($r);
        }

        return $rows;
    }

    private function cellColumn($cell) {
        return preg_replace('/[0-9]/', '', $cell);
    }
}
