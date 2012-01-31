#include<stdio.h>
#include<unistd.h>
#include<string.h>
#include"cJSON.h"
#include<time.h>
#define PATH "/.amd_mnt/gais4/host/home/UserHome/tyl100/"
#define WEBROOT "public_html/"
#define IPC "81601"

char dic[300000][100];
int dic_size;
/*int in_dic(char *str)
{
	int flag=0,i=0;
	for(i=0;i<dic_size;i++)
	{
		if(strcasecmp(dic[i],str)==0)
		{
			flag=1;
			break;
		}
	}
	if(flag==0)
	{
		return -1;
	}
	return i;
}*/
int in_dic(char *value) 
{
	int position;
	int begin = 0; 
	int end = dic_size - 1;
	int cond = 0;

	while(begin <= end) {
		position = (begin + end) / 2;
		if((cond = strcasecmp(dic[position], value)) == 0)
			return position;
		else if(cond < 0)
			begin = position + 1;
		else
			end = position - 1;
	}

	return -1;
}

int main(int argc, char *argv[])
{
	cJSON *root,*record;
	FILE *query_ptr,*page_ptr;
	char ch;
	char query[10000]={0},temp[100000]={0};
	char cmd[100000]={0};
	char file_name[50];
	//	strcpy(cmd,"/home/sweslo17/idb/bin/nSearch -P 81605 -5 ");
	int score=0,page_id=0,count=0;
	int start=0,end=0;
	int i,j,k;
	int flag_q=0,flag_n=0,flag_f=0,flag_r=0;
	while((ch = getopt(argc,argv,"r:q:n:f:"))!=-1)
	{
		switch(ch)
		{

			case 'q':
				strcpy(query,optarg);
				//printf("%s\n",query);
				flag_q=1;
				break;
			case 'n':
				sscanf(optarg,"%d%*c%d",&start,&end);
				flag_n=1;
				break;
			case 'f':
				flag_f=1;
				break;
			case 'r':
				flag_r=1;
				strcpy(query,optarg);
				break;
			default:
				break;
		}
	}
	if(flag_r==1)
	{
		FILE *dic_file,*log_file,*result_pipe;
		char log[50][100];
		char list[100000][100];
		char pre_temp[100000];
		int count_array[100000],check_array[100000];
		int log_count=0,result_count=0,flag=0;
		int max_count=0;
		clock_t start, end;
		double elapsed;

		//char dic[30000][100];
		sprintf(cmd,"%s%ssearch-engine/term/cshell/dic/word_revise",PATH,WEBROOT);
		//printf("%s\n",cmd);
		dic_file = fopen(cmd,"rb");
		if(strcmp(query,"all")==0)
		{
			sprintf(cmd,"%s%ssearch-engine/term/cshell/data/total.log",PATH,WEBROOT);
		}
		else
		{
			sprintf(cmd,"%s%ssearch-engine/term/cshell/data/%s.log",PATH,WEBROOT,query);
		}
		log_file = fopen(cmd,"rb");
		i=0;
		memset(dic,0,sizeof(dic));
		while(fgets(dic[i],100,dic_file)!=NULL)
		{
			dic[i][strlen(dic[i])-1]='\0';
			i++;
		}
		fclose(dic_file);
		dic_size=i;
		i=0;
		while(fscanf(log_file," %s",temp)!=EOF)
		{
			if(in_dic(temp)!=-1)
			{
				strcpy(log[log_count%10],temp);
				log_count++;
			}
		}
		fclose(log_file);
		if(log_count>9)
		{
			log_count=9;
		}
		for(i=0;i<log_count;i++)
		{
			//printf("%ssearch/idb/bin/nSearch -N 100 -P %s -459 -5 %s\n",PATH,IPC,log[i]);
			sprintf(cmd,"%ssearch/idb/bin/nSearch -N 100 -P %s -459 -5 %s\n",PATH,IPC,log[i]);
			memset(check_array,0,sizeof(check_array));
			result_pipe=popen(cmd,"r");
			//result_pipe=fopen("test_input_small","r");
			//start = clock();
			while(fgets(pre_temp,100000,result_pipe)!=NULL)
			{
				//printf("%s\n",pre_temp);
				//start = clock();
				if(strcmp(pre_temp,"@\n")==0)
				{
					memset(check_array,0,sizeof(check_array));
					continue;
				}
				memset(temp,0,sizeof(temp));
				k=0;
				while(sscanf(pre_temp+k," %s",temp)!=EOF)
				{
					k += strlen(temp)+1;
					if(temp[strlen(temp)-1]=='.'||temp[strlen(temp)-1]==','||temp[strlen(temp)-1]==';'||temp[strlen(temp)-1]=='?'||temp[strlen(temp)-1]=='"')
					{
						temp[strlen(temp)-1]='\0';
					}
					if(temp[0]=='.'||temp[0]==','||temp[0]==';'||temp[0]=='?'||temp[0]=='"')
					{
						strcpy(temp,&temp[1]);
					}
					if(in_dic(temp)!=-1)
					{
						flag=0;
						for(j=0;j<result_count;j++)
						{
							if(strcasecmp(list[j],temp)==0)
							{
								flag=1;
								if(check_array[j]==0)
								{
									count_array[j]++;
									check_array[j]=1;
								}
								break;
							}
						}
						if(flag==0)
						{
							strcpy(list[result_count],temp);
							count_array[result_count]=1;
							check_array[result_count]=1;
							result_count++;
						}
					}
				}
				//end = clock();
				//printf("%lf\n",((double) (end - start)) / CLOCKS_PER_SEC);
			}
			//			end = clock();
			//                        printf("%lf",((double) (end - start)) / CLOCKS_PER_SEC);
			fclose(result_pipe);
		}
		//printf("%d",result_count);
		for(i=0;i<result_count;i++)
		{
			//printf("%s\t%d\n",list[i],count_array[i]);
			if(max_count<count_array[i])
			{
				max_count=count_array[i];
				strcpy(temp,list[i]);
			}
		}
		//printf("max: %s\t%d",temp,max_count);
		sprintf(cmd,"%ssearch/idb/bin/nSearch -N 10 -P %s -459 -5 %s\n",PATH,IPC,temp);
		//printf("%s",cmd);
		result_pipe=popen(cmd,"r");
		//result_pipe=fopen("test_input","r");
		root=cJSON_CreateArray();
		while(fgets(temp,1000,result_pipe))
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
			else if(strstr(temp,"@viewCount:"))
			{
				cJSON_AddStringToObject(record,"viewCount",&temp[11]);
			}
			else if(strstr(temp,"@category:"))
			{
				cJSON_AddStringToObject(record,"category",&temp[10]);
			}
			else if(strstr(temp,"@author:"))
			{
				cJSON_AddStringToObject(record,"author",&temp[8]);
			}
			else if(strstr(temp,"@src:"))
			{
				cJSON_AddStringToObject(record,"src",&temp[5]);
			}
		}
		printf("%s\n",cJSON_Print(root));
		cJSON_Delete(root);
		pclose(query_ptr);
		//printf("%d\n",in_dic("apple"));
	}
	if(flag_q==1)
	{
		//query_ptr=popen(cmd,"r");
		//fgets(temp,1000,query_ptr);
		sprintf(cmd,"%s/search/idb/bin/nSearch -N 500 -P %s -459 -5 \'%s\'",PATH,IPC,query);
		//printf("%s",cmd);
		page_ptr=popen(cmd,"r");
		//page_ptr=fopen("test_input","r");
		root=cJSON_CreateArray();
		while(fgets(temp,1000,page_ptr))
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
			else if(strstr(temp,"@viewCount:"))
			{
				cJSON_AddStringToObject(record,"viewCount",&temp[11]);
			}
			else if(strstr(temp,"@category:"))
			{
				cJSON_AddStringToObject(record,"category",&temp[10]);
			}
			else if(strstr(temp,"@author:"))
			{
				cJSON_AddStringToObject(record,"author",&temp[8]);
			}
			else if(strstr(temp,"@src:"))
			{
				cJSON_AddStringToObject(record,"src",&temp[5]);
			}
		}
		printf("%s\n",cJSON_Print(root));
		cJSON_Delete(root);
		pclose(query_ptr);
	}
	return 0;
}
