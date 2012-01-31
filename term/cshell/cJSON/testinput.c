#include <stdio.h> 
#include <stdlib.h> 
#include <string.h>
#include "cJSON.h"

/*********************************** 
 * Author : Demon 
 * Website : http://demon.tw 
 * E- mail : 380401911@qq.com 
 * ***********************************/

/* {{{ proto string addslashes(string str) 
 *    Escapes single quote, double quotes and backslash characters in a string with backslashes */ 
char *addslashes( char *str) 
{ 
	/* maximum string length, worst case situation */ 
	char * new_str; 
	char *source, *target; 
	char *end; 
	int new_length; 
	int length = strlen(str);

	if (length == 0 ) { 
		char *p = ( char *) malloc( 1 ); 
		if (p == NULL) { 
			return p; 
		} 
		p[ 0 ] = 0 ; 
		return p; 
	}

	new_str = ( char *) malloc( 2 * length + 1 ); 
	if (new_str == NULL) { 
		return new_str; 
	} 
	source = str; 
	end = source + length; 
	target = new_str;

	while (source < end) { 
		switch (*source) { 
			case '\0' : 
				*target++ = '\\' ; 
				*target++ = '0' ; 
				break ; 
			case '\'' : 
			case '\"' : 
			case '\\' : 
				*target++ = '\\' ; 
				/* break is missing *intentionally* */ 
			default : 
				*target++ = *source; 
				break ; 
		} 
		source++; 
	}

	*target = 0 ; 
	new_length = target - new_str; 
	new_str = ( char *) realloc(new_str, new_length + 1 ); 
	return new_str; 
} 
/* }}} */

int main() 
{ 
	//char *str = addslashes( "Is your name\" O'reilly?" ); 
	//printf( "%s\n" , str); 
	//free(str); 
	FILE *input=NULL;
	char temp[10000];
	cJSON *root,*record;
	input=fopen("test_input","r");
	root=cJSON_CreateArray();
	while(fgets(temp,10000,input)!=NULL)
	{
		if(strcmp(temp,"@\n")==0)
		{
			cJSON_AddItemToArray(root,record=cJSON_CreateObject());
		}
		else if(strstr(temp,"@id:"))
		{
			cJSON_AddStringToObject(record,"id",&temp[4]);
		}
		else if(strstr(temp,"@title:"))
		{
			cJSON_AddStringToObject(record,"title",&temp[7]);
		}
	}
	printf("%s\n",cJSON_Print(root));
	cJSON_Delete(root);
	return 0 ; 
}
