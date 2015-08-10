<div id="{$container}-messages-scroll" style="height: 100%; overflow-y: auto;">
  <table style="width: 100%; height: 100%;" cellpadding="0" cellspacing="0">
    <tbody>
      <tr style="height: 100%;">
        <td valign="top">
          <div id="{$container}-messages" style="width: 100%; vertical-align: top;"></div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<form onsubmit="chatc.send(); return false;" style="margin: 0">
  <table cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td style="width: 100%"><input id="{$container}-message" style="width: 100%"/></td>
        <td><input type="submit" value="+"/></td>
      </tr>
    </tbody>
  </table>
</form>