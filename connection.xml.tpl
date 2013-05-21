<?xml version="1.0" encoding="UTF-8"?>
<config>
  <propel>
    <datasources default="depending">
      <datasource id="depending">
        <adapter>mysql</adapter>
        <connection>
          <dsn>mysql:host=localhost;dbname=depending</dsn>
          <user>travis</user>
        </connection>
      </datasource>
    </datasources>
  </propel>
</config>