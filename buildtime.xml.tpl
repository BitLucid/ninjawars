<?xml version="1.0" encoding="UTF-8"?>
<config>
  <propel>
    <datasources default="ninjawars">
      <datasource id="ninjawars">
        <adapter>pgsql</adapter>
        <connection>
          <dsn>pgsql:dbname=nw</dsn>
          <user>postgres</user>
          <password>postgres</password>
        </connection>
      </datasource>
    </datasources>
  </propel>
</config>