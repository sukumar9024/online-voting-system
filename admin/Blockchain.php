<?php

class Blockchain {
    public function calculateHash($data) {
        return hash('sha256', $data);
    }

    public function createBlock($previousHash, $data) {
        $timestamp = time();
        $blockData = json_encode([
            'previous_hash' => $previousHash,
            'data' => $data,
            'timestamp' => $timestamp
        ]);
        $blockHash = $this->calculateHash($blockData);
        return [
            'hash' => $blockHash,
            'previous_hash' => $previousHash,
            'data' => $data,
            'timestamp' => $timestamp
        ];
    }

    public function addBlockToDatabase($mysqli, $block) {
        $stmt = $mysqli->prepare("INSERT INTO blockchain (block_hash, previous_hash, data, timestamp) VALUES (?, ?, ?, FROM_UNIXTIME(?))");
        $stmt->bind_param("sssi", $block['hash'], $block['previous_hash'], $block['data'], $block['timestamp']);
        $stmt->execute();
        $stmt->close();
    }

    public function getLastBlockHash($mysqli) {
        $result = $mysqli->query("SELECT block_hash FROM blockchain ORDER BY id DESC LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['block_hash'];
        }
        return null;
    }
}

?>
