<p align="center">
<img src="https://cdn.discordapp.com/avatars/1227531295693733919/ed9ebcf59dd9edacd425b6a0ec986407?size=512">
</p>
<h1 align="center">Hephaestus</h1>

<ul>
    <li> Define Interaction Handlers on the move</li>
    <li> Defining APPLICATION_COMMAND</li>
        <ol>
            <li> Creating a file in the good directory (currently: `/app/InteractionHandlers/SlashCommands`)</li> 
            <li> Extending the \App\Framework\InteractionHandlers\ApplicationCommands\AbstractSlashCommand in your new php class.</li>
            <li> You must specify `name` and `description` property.</li>
            <li> The `handle` method will be invoked when the bot receive an interaction associated with this handler.</li>
        </ol>
</ul>

<h1>TODO:</h1>
<ul>
    <li>Support handling for
        <ul>
            <li>☑️ APPLICATION_COMMAND. (Not totally implemented).</li>
            <li>❌ PING interactions. </li>
            <li>❌ APPLICATION_COMMAND_AUTOCOMPLETE interaction</li>
            <li>❌ MODAL_SUBMIT interaction</li>
            <li>☑️ MESSAGE_COMPONENT interaction. (Not totally implemented).</li>
        </ul>
    </li>
</ul>
