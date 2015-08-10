{assign var=size value=50}

{assign var=cshow value=0}
{if $board->getPlayer() == $board->getRPlayer() && $board->isActive() == 1}
{assign var=cshow value=1}
{/if}

<table>
  <tr>
    <td>
      {if $board->getRPlayer() == 0}
      <table cellpadding="0" cellspacing="0">
        <tbody>
          <tr style="height: 25px;">
            <td>&nbsp;</td>
            <td align="center" valign="middle">a</td>
            <td align="center" valign="middle">b</td>
            <td align="center" valign="middle">c</td>
            <td align="center" valign="middle">d</td>
            <td align="center" valign="middle">e</td>
            <td align="center" valign="middle">f</td>
            <td align="center" valign="middle">g</td>
            <td align="center" valign="middle">h</td>
            <td>&nbsp;</td>
          </tr>
          {section name=x start=9 loop=8 step=-1}
          <tr style="height: {$size}px;">
            {math assign=x equation="x+1" x=$smarty.section.x.index}
            <td align="center" width="25px">{$x}</td>
            {section name=y start=1 loop=9 step=1}
            {math assign=y equation="y" y=$smarty.section.y.index}
            {math assign=ind equation="x+y" x=$x y=$y}
            {assign var=cell value=$board->getCell($x, $y)}
            <td id="item{$x}{$y}" width="{$size}px" style="border: 1px black solid; margin: 0; padding: 0;{if $ind is even} background: brown;{/if}" align="center" valign="middle">
              {if $cell}
              <img width="{$size}px" height="{$size}px" src="images/{$cell->getOwner()}{$cell->getType()}.gif"/>
              {else}
              &nbsp;
              {/if}
            </td>
            {/section}
            <td align="center" width="25px">{$x}</td>
          </tr>
          {/section}
          <tr style="height: 25px;">
            <td>&nbsp;</td>
            <td align="center" valign="middle">a</td>
            <td align="center" valign="middle">b</td>
            <td align="center" valign="middle">c</td>
            <td align="center" valign="middle">d</td>
            <td align="center" valign="middle">e</td>
            <td align="center" valign="middle">f</td>
            <td align="center" valign="middle">g</td>
            <td align="center" valign="middle">h</td>
            <td>&nbsp;</td>
          </tr>
        </tbody>
      </table>
      {else}
      <table cellpadding="0" cellspacing="0">
        <tbody>
          <tr style="height: 25px;">
            <td>&nbsp;</td>
            <td align="center" valign="middle">h</td>
            <td align="center" valign="middle">g</td>
            <td align="center" valign="middle">f</td>
            <td align="center" valign="middle">e</td>
            <td align="center" valign="middle">d</td>
            <td align="center" valign="middle">c</td>
            <td align="center" valign="middle">b</td>
            <td align="center" valign="middle">a</td>
            <td>&nbsp;</td>
          </tr>
          {section name=x start=1 loop=9 step=1}
          <tr style="height: {$size}px;">
            {math assign=x equation="x" x=$smarty.section.x.index}
            <td align="center" width="25px">{$x}</td>
            {section name=y start=9 loop=8 step=-1}
            {math assign=y equation="y + 1" y=$smarty.section.y.index}
            {math assign=ind equation="x+y" x=$x y=$y}
            {assign var=cell value=$board->getCell($x, $y)}
            <td id="item{$x}{$y}" width="{$size}px" style="border: 1px black solid;{if $ind is even} background: brown;{/if}" align="center" valign="middle">
              {if $cell}
              <img width="{$size}px" height="{$size}px" src="images/{$cell->getOwner()}{$cell->getType()}.gif"/>
              {else}
              &nbsp;
              {/if}
            </td>
            {/section}
            <td align="center" width="25px">{$x}</td>
          </tr>
          {/section}
          <tr style="height: 25px;">
            <td>&nbsp;</td>
            <td align="center" valign="middle">h</td>
            <td align="center" valign="middle">g</td>
            <td align="center" valign="middle">f</td>
            <td align="center" valign="middle">e</td>
            <td align="center" valign="middle">d</td>
            <td align="center" valign="middle">c</td>
            <td align="center" valign="middle">b</td>
            <td align="center" valign="middle">a</td>
            <td>&nbsp;</td>
          </tr>
        </tbody>
      </table>
      {/if}
    </td>
    <td valign="top">
      Вы играете {if $board->getRPlayer() == 0}белыми{else}чёрными{/if} фигурами.
      <br/>
      {if $board->getRPlayer() == 0}
      {dataloader->getUser id=$board->getSplId() assign=opponent}
      {else}
      {dataloader->getUser id=$board->getFplId() assign=opponent}
      {/if}
      Ваш противник {$opponent->getLogin()}.
      <br/>
      {assign var=curturn value=$board->getTurn()}
      {if $board->getPlayer() != $board->getRPlayer()}
      {math assign=curturn equation="x+1" x=$curturn}
      {/if}
      {math assign=cturn equation="(x+1)/2" x=$curturn format="%d"}
      Ход № {$cturn}, {if $curturn % 2 != 0}белые{else}черные{/if} ({$curturn}).
      <br/>
      <br/>

      {if $message}
      {$message}<br/>
      {/if}

      {if $board->isActive() != 1}
      {if $board->getWin() == 2}
      Ничья! <br/>
      {elseif $board->getWin() == 3}
      Пат! Нету ходов =(
      {elseif $board->getWin() == 4}
      Ничья! Никто не может поставить мат =(
      {elseif $board->getWin() == 5}
      Ничья! 50 ходов без взятия и хода пешкой.
      {elseif $board->getWin() == 6}
      Ничья! Троекратное повторение позиции.
      {elseif $board->getRPlayer() == $board->getWin()}
      Вы победили!
      {else}
      Увы, проигрыш =(
      {/if}
      <br/>
      <a href="#" onclick="rqd.doGateWayRequest( '{$WEB_LOCATION}', 'exit', {ldelim} fightId: {$board->getId()}, container: '{$container}' {rdelim} ); return false;">Выход</a>
      <br/>
      {else}
      {if $check}
      ШАХ!<br/>
      <script>alert('Шах!');</script>
      {/if}

      {if $board->getPlayer() == $board->getRPlayer()}
      Кого ставить вместо пешки: {html_options name=rpawn options=$fig_types selected=$rpawn}
      <br/>
      {else}
      <input name="rpawn" value="{$rpawn}" type="hidden"/>
      {/if}
      <br/>

      {if $board->getPlayer() == $board->getRPlayer()}
      Ваш ход.
      <br/>
      <a href="#" onclick="rqd.doGateWayRequest( '{$WEB_LOCATION}', 'defeate', {ldelim} fightId: {$board->getId()}, container: '{$container}' {rdelim} ); return false;">Сдаться</a>
      <br/>
      {else}
      Ожидаем хода противника...
      {/if}
      {/if}

    </td>
  </tr>
</table>
