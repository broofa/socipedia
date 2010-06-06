<?= '<?' ?>xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.1">
  <Document>
  <? foreach ($entries as $entry) { ?>
    <Placemark>
      <name><?= htmlify($entry->getDisplayName(), false) ?></name>
      <description><![CDATA[
        <?= $entry->getDescription(80) ?>
        <br/>
        <?= anchor('/entries/'.$entry->id, 'Click for details') ?>
      ]]></description>
      <address>
        <?= htmlify($entry->address, false) ?>
      </address>
      <Point>
        <coordinates><?= $entry->geocode ?></coordinates>
      </Point>
    </Placemark>
  <? } ?>
  </Document>
</kml>
