CREATE TABLE log (
    id INT AUTO_INCREMENT NOT NULL,
    owner_id INT DEFAULT NULL,
    job_id INT DEFAULT NULL,
    reference VARCHAR(190) DEFAULT '' NOT NULL,
    severity INT DEFAULT 0 NOT NULL,
    message LONGTEXT NOT NULL,
    context LONGTEXT NOT NULL COMMENT '(DC2Type:json_array)',
    created DATETIME NOT NULL,
    INDEX owner_idx (owner_id),
    INDEX job_idx (job_id),
    INDEX reference_idx (reference),
    INDEX severity_idx (severity),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE log ADD CONSTRAINT FK_8F3F68C57E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE;
