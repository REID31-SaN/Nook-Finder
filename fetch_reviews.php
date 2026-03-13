<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'config.php';

$is_ajax = isset($_GET['ajax']) ? true : false;

if ($is_ajax) {
    $place_id = isset($_GET['place_id']) ? intval($_GET['place_id']) : 0;
    $sort = $_GET['sort'] ?? 'recent';
    
    $current_user_id = $_SESSION['user_id'] ?? 0;
    $user_type = 'User';
    if ($current_user_id > 0 && isset($conn)) {
        $u_stmt = $conn->prepare("SELECT Type FROM accounts WHERE account_id = ?");
        $u_stmt->bind_param("i", $current_user_id);
        $u_stmt->execute();
        if($u_row = $u_stmt->get_result()->fetch_assoc()) {
            $user_type = $u_row['Type'];
        }
    }
}

$order_clause = "ORDER BY r.created_at DESC"; 
if (isset($sort) && $sort === 'popular') {
    $order_clause = "ORDER BY likes_count DESC, r.rating DESC, r.created_at DESC"; 
}

if (isset($place_id) && $place_id > 0 && isset($conn)) {
    $rev_query = "SELECT r.*, a.username, a.profile_pic, 
                  (SELECT COUNT(*) FROM review_likes rl WHERE rl.review_id = r.id) AS likes_count,
                  (SELECT COUNT(*) FROM review_likes rl2 WHERE rl2.review_id = r.id AND rl2.account_id = ?) AS user_liked
                  FROM reviews r 
                  JOIN accounts a ON r.account_id = a.account_id 
                  WHERE r.place_id = ? 
                  $order_clause";
                  
    $rev_stmt = $conn->prepare($rev_query);
    $rev_stmt->bind_param("ii", $current_user_id, $place_id);
    $rev_stmt->execute();
    $reviews = $rev_stmt->get_result();
    
    if($reviews && $reviews->num_rows > 0) {
        while($rev = $reviews->fetch_assoc()) {
            ?>
            <div class="review-card">
                <div class="review-top">
                    <div class="reviewer-info">
                        <img src="<?= !empty($rev['profile_pic']) ? htmlspecialchars($rev['profile_pic']) : 'images/default-user.jpg' ?>" class="reviewer-img">
                        <div>
                            <div class="reviewer-name">
                                <?= htmlspecialchars($rev['username']) ?> 
                                <?php if($user_type === 'Admin' && $rev['account_id'] != $current_user_id): ?>
                                    <span style="font-size:0.75rem; color:#a71d2a; font-weight:normal; margin-left:5px;">(Admin view)</span>
                                <?php endif; ?>
                            </div>
                            <div class="review-date"><?= date('M j, Y g:i A', strtotime($rev['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="review-rating">
                        <span class="stars"><?= str_repeat('★', $rev['rating']) ?><?= str_repeat('☆', 5 - $rev['rating']) ?></span>
                    </div>
                </div>
                
                <div class="review-body">
                    <p><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                </div>

                <div class="review-actions">
                    <?php $isLiked = $rev['user_liked'] > 0; ?>
                    <button type="button" class="like-btn <?= $isLiked ? 'liked' : '' ?>" onclick="toggleReviewLike(this, <?= $rev['id'] ?>)">
                        <span class="like-icon"><?= $isLiked ? '❤️' : '🤍' ?></span> 
                        <span class="like-count"><?= $rev['likes_count'] ?></span> Likes
                    </button>

                    <?php if($rev['allow_replies'] == 1): ?>
                        <button type="button" class="reply-btn" onclick="toggleReplyForm(<?= $rev['id'] ?>)">💬 Reply</button>
                    <?php endif; ?>
                    
                    <?php if($current_user_id > 0 && ($current_user_id == $rev['account_id'] || $user_type === 'Admin')): ?>
                        <form method="POST" action="" style="margin-left:auto;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                            <button type="submit" name="delete_review" class="delete-btn">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php 
                    $rep_stmt = $conn->prepare("SELECT rr.*, a.username, a.profile_pic FROM review_replies rr JOIN accounts a ON rr.account_id = a.account_id WHERE rr.review_id = ? ORDER BY rr.created_at ASC");
                    $rep_stmt->bind_param("i", $rev['id']);
                    $rep_stmt->execute();
                    $replies = $rep_stmt->get_result();
                ?>
                
                <?php if($replies->num_rows > 0 || ($rev['allow_replies'] == 1 && $current_user_id > 0)): ?>
                    <div class="replies-section">
                        <?php while($reply = $replies->fetch_assoc()): ?>
                            <div class="reply-card">
                                <img src="<?= !empty($reply['profile_pic']) ? htmlspecialchars($reply['profile_pic']) : 'images/default-user.jpg' ?>" class="reply-img">
                                <div class="reply-content">
                                    <div class="reply-header">
                                        <span class="reply-name"><?= htmlspecialchars($reply['username']) ?></span>
                                        <span class="reply-date"><?= date('M j, g:i A', strtotime($reply['created_at'])) ?></span>
                                    </div>
                                    <div class="reply-text"><?= nl2br(htmlspecialchars($reply['reply_text'])) ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <?php if($rev['allow_replies'] == 1 && $current_user_id > 0): ?>
                            <form method="POST" action="" class="reply-form" id="reply-form-<?= $rev['id'] ?>" style="display:none;">
                                <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                <div style="display:flex; gap:10px;">
                                    <input type="text" name="reply_text" class="reply-input" placeholder="Write a reply..." required autocomplete="off">
                                    <button type="submit" name="submit_reply" class="submit-btn" style="padding: 8px 15px;">Send</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    } else {
        echo '<p style="text-align: center; color: #888; margin-top: 30px;">No reviews yet. Be the first to share your experience!</p>';
    }
}
?>