DROP TABLE IF EXISTS publisher;
CREATE TABLE publisher
(
    id    INT PRIMARY KEY AUTO_INCREMENT,
    name  VARCHAR(20)  NOT NULL,
    email VARCHAR(320) NOT NULL
);
DROP TABLE IF EXISTS optProps;

CREATE TABLE optProps
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    threshold      INT         NOT NULL,
    sourceEvent    VARCHAR(20) NOT NULL,
    measuredEvent  VARCHAR(20) NOT NULL,
    ratioThreshold FLOAT       NOT NULL
);

DROP TABLE IF EXISTS campaign;
CREATE TABLE campaign
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(20) NOT NULL,
    optPropsId INT,
    CONSTRAINT fk_optPropsId
        FOREIGN KEY (optPropsId)
            REFERENCES optProps (id)

);
DROP TABLE IF EXISTS publisherBlacklist;
CREATE TABLE publisherBlacklist
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id  INT NOT NULL,
    publisher_id INT NOT NULL,
    CONSTRAINT fk_campaign_id
        FOREIGN KEY (campaign_id)
            REFERENCES campaign (id),
    CONSTRAINT fk_publisher_id
        FOREIGN KEY (publisher_id)
            REFERENCES publisher (id)
);

DROP TABLE IF EXISTS Event;
CREATE TABLE Event
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    type         varchar(20) NOT NULL,
    campaign_id  int         NOT NULL,
    publisher_id int         NOT NULL,
    ts           TIMESTAMP,
    CONSTRAINT fk_campaign
        FOREIGN KEY (campaign_id)
            REFERENCES campaign (id),
    CONSTRAINT fk_publisher
        FOREIGN KEY (publisher_id)
            REFERENCES publisher (id)

);
