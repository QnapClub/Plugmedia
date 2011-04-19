{$name}

Recent activity from Plugmedia
-----------------------------------------------------------

{foreach from=$directory_array item=dir}
There is new files or directory in the folder {$dir.name|default:'Root'}
- {$dir.inserted_file} new files
- {$dir.inserted_directory} new directory
- {$dir.updated_file} updated files

{/foreach}

{t}FOLLOW_LINK{/t}:
{$url_plugmedia}
