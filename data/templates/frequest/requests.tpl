<a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'create_request');return false">Подать заявку на партию</a>

{if count($all_requests) > 0}
<table>
  <thead>
    <th>Первый игрок</th>
    <th>Второй игрок</th>
    <th>&nbsp;</th>
  </thead>
  <tbody>
    {foreach from=$all_requests item=request}
      {dataloader->getUser id=$request->getFplId() assign=fpl}
      {dataloader->getUser id=$request->getSplId() assign=spl}
      <tr>
        <td>{$fpl->getLogin()}</td>
        <td>{if $request->getSplId() > 0 && spl}{$spl->getLogin()}{else}&nbsp;{/if}</td>
        <td>
          {if $user->getId() == $request->getFplId()}
            {if $request->getSplId() != 0}
              <a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'accept_request', {ldelim} request: {$request->getId()} {rdelim}); return false">Принять</a>
              /
              <a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'reject_request', {ldelim} request: {$request->getId()} {rdelim}); return false">Отклонить</a>
              /
            {/if}
            <a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'cancel_request', {ldelim} request: {$request->getId()} {rdelim}); return false">Отменить</a>
          {else}
            {if $request->getSplId() == 0}
              <a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'attach_request', {ldelim} request: {$request->getId()} {rdelim}); return false">Присоединиться</a>
            {elseif $request->getSplId() == $user->getId()}
              <a href="#" onclick="rqd.doGateWayRequest('{$WEB_LOCATION}', 'unattach_request', {ldelim} request: {$request->getId()} {rdelim});return false">Отказаться</a>
            {else}
            &nbsp;
            {/if}
          {/if}
        </td>
      </tr>
    {/foreach}
  </tbody>
</table>
{/if}

{if $error_report}
<br/>
<font style="color: red">{$error_report}</font>
{/if}
