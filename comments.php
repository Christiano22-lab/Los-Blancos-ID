<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' && isset($_SESSION['user_id'])) {
        $news_id = $_POST['news_id'];
        $comment = $_POST['comment'];
        $user_id = $_SESSION['user_id'];
        
        if (!empty($comment) && !empty($news_id)) {
            $db->query('INSERT INTO comments (news_id, user_id, comment) VALUES (:news_id, :user_id, :comment)');
            $db->bind(':news_id', $news_id);
            $db->bind(':user_id', $user_id);
            $db->bind(':comment', $comment);
            
            if ($db->execute()) {
                echo json_encode(['success' => true, 'message' => 'Komentar berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Gagal menambahkan komentar']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Komentar tidak boleh kosong']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    }
}

if ($method === 'GET') {
    $news_id = $_GET['news_id'] ?? '';
    
    if (!empty($news_id)) {
        $db->query('SELECT c.*, u.full_name, u.username FROM comments c 
                    LEFT JOIN users u ON c.user_id = u.id 
                    WHERE c.news_id = :news_id 
                    ORDER BY c.created_at DESC');
        $db->bind(':news_id', $news_id);
        $comments = $db->resultset();
        
        echo json_encode(['success' => true, 'data' => $comments]);
    } else {
        echo json_encode(['success' => false, 'error' => 'News ID required']);
    }
}
?>