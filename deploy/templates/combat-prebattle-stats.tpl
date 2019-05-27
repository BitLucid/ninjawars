{**
 * Shows a table of attacker/target stats for use before combat
 *}
    <table border="0">
      <tr>
        <th colspan="3">Before the Attack</th>
      </tr>
      <tr>
        <td>Name</td>
        <td>STR</td>
        <td>HP</td>
      </tr>
      <tr>
        <td>{$attacker->name()|escape}</td>
        <td>{$attacker->getStrength()|escape}</td>
        <td>{$attacker->health|escape}</td>
      </tr>
      <tr>
        <td>{$target->name()|escape}</td>
        <td>{$target->getStrength()|escape}</td>
        <td>{$target->health|escape}</td>
      </tr>
    </table>
