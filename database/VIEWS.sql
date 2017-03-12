# ((-x+24*3600)/log(x))/64
CREATE VIEW `posts_bonus` AS
SELECT
	post_id,
    IF (UNIX_TIMESTAMP() - post_publish_time < 24*3600, 
        TRUNCATE((24*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(POW(UNIX_TIMESTAMP() - post_publish_time, 64)), 2)
        , 0)
		 AS post_bonus
FROM
	posts
;

# ((-x+96*3600)/log(x))/64
CREATE VIEW `comments_score` AS
SELECT
	comments.post_id AS post_id,
    comments.comment_id AS comment_id,
	IF (UNIX_TIMESTAMP() - comment_time < 96*3600, 
        TRUNCATE((96*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(POW(UNIX_TIMESTAMP() - post_publish_time, 64)), 2)
        , 0)
		 AS comment_score
FROM
	comments
JOIN posts ON
	posts.post_id = comments.post_id
;

#((-x+72*3600)/log(x))/64
CREATE VIEW `likes_score` AS
SELECT
	post_likes.post_id AS post_id,
	IF (UNIX_TIMESTAMP() - like_time < 72*3600, 
        TRUNCATE((72*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(POW(UNIX_TIMESTAMP() - post_publish_time, 64)), 2)
        , 0)
		 AS like_score
FROM
	post_likes
JOIN posts ON
	posts.post_id = post_likes.post_id
;

#((-x+48*3600)/log(x))/64
CREATE VIEW `views_score` AS
SELECT
	post_views.post_id AS post_id,
	IF (UNIX_TIMESTAMP() - view_time < 48*3600, 
        TRUNCATE((48*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(POW(UNIX_TIMESTAMP() - post_publish_time, 64)), 2)
        , 0)
		 AS view_score
FROM
	post_views
JOIN posts ON
	posts.post_id = post_views.post_id
;

CREATE VIEW `pop_score` AS
SELECT
	posts.post_id AS post_id,
	(
        (IF ((SELECT COUNT(*) FROM comments_score WHERE comments_score.post_id = posts.post_id) > 0, (SELECT SUM(comment_score) FROM comments_score WHERE comments_score.post_id = posts.post_id), 0))
      + (IF ((SELECT COUNT(*) FROM likes_score WHERE likes_score.post_id = posts.post_id) > 0, (SELECT SUM(like_score) FROM likes_score WHERE likes_score.post_id = posts.post_id), 0))
      + (IF ((SELECT COUNT(*) FROM views_score WHERE views_score.post_id = posts.post_id) > 0, (SELECT SUM(view_score) FROM views_score WHERE views_score.post_id = posts.post_id), 0))
      + (SELECT post_bonus FROM posts_bonus WHERE posts_bonus.post_id = posts.post_id LIMIT 1)
    ) AS post_score
FROM
    posts
;
