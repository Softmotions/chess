    {if $error}
      <div style="color: red">
        {foreach from=$error item=item}
          {$item}<br/>
        {/foreach}
      </div>
    {/if}
