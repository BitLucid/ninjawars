<?xml version="1.0" encoding="UTF-8"?>
<config>
  <propel>
    <datasources default="ninjawars">
      <datasource id="ninjawars">
        <adapter>pgsql</adapter>
        <connection>
          <dsn>pgsql:dbname=nw</dsn>
          <user>ninjamaster</user>
        </connection>
      </datasource>
    </datasources>
  </propel>
</config>