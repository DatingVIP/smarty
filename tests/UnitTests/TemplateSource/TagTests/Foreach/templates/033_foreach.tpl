{strip}
{foreach ['a','b'] as $a}
{foreach [1,2,3,4,5] as $i}
    {if $i == 3}{continue}{/if}
    {$a}{$i}
{/foreach}
{/foreach}