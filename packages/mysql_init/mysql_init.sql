CREATE DATABASE IF NOT EXISTS cerebro;

GRANT ALL PRIVILEGES ON cerebro.* TO 'root' @'%';

CREATE DATABASE IF NOT EXISTS cerebro_audit;

GRANT ALL PRIVILEGES ON cerebro_audit.* TO 'root' @'%';

CREATE DATABASE IF NOT EXISTS cerebro_test;

GRANT ALL PRIVILEGES ON cerebro_test.* TO 'root' @'%';

-- for docker only
GRANT ALL PRIVILEGES ON *.* TO 'root' @'%' WITH GRANT OPTION;