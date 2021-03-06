CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `break_after` time NOT NULL,
  `break_duration` time NOT NULL,
  `time_to_work` time NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `id_user_UNIQUE` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
INSERT INTO `users` VALUES
(1,'demo','changeme','06:00:00','00:45:00','07:30:00');

CREATE TABLE `tracker` (
  `id_tracker` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id_tracker`,`date`),
  UNIQUE KEY `user_date` (`user_id`,`date`),
  KEY `fk_user_id_idx` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `tracker_overtime` (
  `id_tracker_overtime` int(11) NOT NULL AUTO_INCREMENT,
  `tracker_id` int(11) NOT NULL,
  `overtime` time DEFAULT NULL,
  `overtime_total` time DEFAULT NULL,
  PRIMARY KEY (`id_tracker_overtime`,`tracker_id`),
  UNIQUE KEY `tracker_id_UNIQUE` (`tracker_id`),
  UNIQUE KEY `id_tracker_overtime_UNIQUE` (`id_tracker_overtime`),
  CONSTRAINT `fk_tracker_id` FOREIGN KEY (`tracker_id`) REFERENCES `tracker` (`id_tracker`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;


DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `delete_entry`(in var_tracker_id INT, in var_user_id INT)
BEGIN
                SET @date = (SELECT date FROM tracker WHERE id_tracker = var_tracker_id AND user_id = var_user_id);
        DELETE FROM tracker WHERE id_tracker = var_tracker_id AND user_id = var_user_id;

        UPDATE tracker as t1 LEFT JOIN tracker_overtime as to1 ON id_tracker = tracker_id SET overtime_total = ADDTIME(to1.overtime, (
                        SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(overtime))) as overtime_total FROM tracker LEFT JOIN tracker_overtime ON id_tracker = tracker_id WHERE user_id = var_user_id AND date < t1.date GROUP BY user_id)
                ) WHERE user_id=var_user_id AND date > @date;
END ;;
DELIMITER ;

DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `insert_entry`(in var_user_id INT, in var_date DATE, in var_start_time TIME, in var_end_time TIME)
BEGIN
        CREATE TEMPORARY TABLE overtime_total_calc_temp
        SELECT date, SEC_TO_TIME(SUM(TIME_TO_SEC(overtime))) as overtime_total FROM tracker LEFT JOIN tracker_overtime ON id_tracker = tracker_id WHERE user_id = var_user_id AND date < var_date GROUP BY user_id;

        SET     @overtime = IF (SUBTIME(var_end_time, var_start_time) <= (SELECT break_after FROM users WHERE id_user=var_user_id),
                SUBTIME(SUBTIME(var_end_time, var_start_time), (SELECT time_to_work FROM users where id_user=var_user_id)),
                SUBTIME(var_end_time, ADDTIME(var_start_time, (SELECT ADDTIME(break_duration, time_to_work) FROM users WHERE id_user=var_user_id)))),
        @overtime_total = IF(ISNULL((SELECT overtime_total FROM overtime_total_calc_temp)),
                @overtime,
                ADDTIME(@overtime, (SELECT overtime_total FROM overtime_total_calc_temp)));

        INSERT INTO tracker (user_id, date, start_time, end_time) VALUES (var_user_id, var_date, var_start_time, var_end_time) ON DUPLICATE KEY UPDATE start_time = var_start_time, end_time = var_end_time;

        SET @tracker_id = (SELECT id_tracker FROM tracker WHERE user_id = var_user_id AND date = var_date AND start_time = var_start_time AND end_time = var_end_time);

        INSERT INTO tracker_overtime (tracker_id, overtime, overtime_total) VALUES (@tracker_id, @overtime, @overtime_total) ON DUPLICATE KEY UPDATE overtime = @overtime, overtime_total = @overtime_total;

        DROP TEMPORARY TABLE overtime_total_calc_temp;

        UPDATE tracker as t1 LEFT JOIN tracker_overtime as to1 ON id_tracker = tracker_id SET overtime_total = ADDTIME(to1.overtime, (
                SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(overtime))) as overtime_total FROM tracker LEFT JOIN tracker_overtime ON id_tracker = tracker_id WHERE user_id = var_user_id AND date < t1.date GROUP BY user_id)
        ) WHERE user_id=var_user_id AND date > var_date;

END ;;
DELIMITER ;