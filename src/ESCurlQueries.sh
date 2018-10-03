#!/usr/bin/env bash

# Read configuration of Elasticsearch
curl -XGET "http://localhost:9200"

# Providing a Stop Analyzer for an English text
curl -X POST "localhost:9200/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "stop",
  "text": "The 2 QUICK Brown-Foxes jumped over the lazy dog\u0027s bone."
}
'

# Creating an Index with a Stop Analyzer with a stop word list
curl -X PUT "localhost:9200/my_index" -H 'Content-Type: application/json' -d'
{
  "settings": {
    "analysis": {
      "analyzer": {
        "my_stop_analyzer": {
          "type": "stop",
          "stopwords": ["the", "over"]
        }
      }
    }
  }
}
'
# Using the index to analyze a test text
curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "my_stop_analyzer",
  "text": "The 2 QUICK Brown-Foxes jumped over the lazy dog\u0027s bone."
}
'

# deleting the index
curl -X DELETE "localhost:9200/my_index"

# Creating an index with several analyzers and filters
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop" -H 'Content-Type: application/json' -d'
{
   "settings":{
      "index":{
         "number_of_shards":1,
         "number_of_replicas":1
      },
      "analysis":{
         "filter":{
            "german_decompounder":{
               "type":"hyphenation_decompounder",
               "word_list_path":"analysis/dictionary-de.txt",
               "hyphenation_patterns_path":"analysis/de_DR.xml",
               "only_longest_match":true,
               "min_subword_size":3
            },
            "german_dict_decompounder":{
               "type":"dictionary_decompounder",
               "word_list_path":"analysis/dictionary-de.txt",
               "hyphenation_patterns_path":"analysis/de_DR.xml",
               "only_longest_match":true,
               "min_subword_size":3
            },
            "german_stemmer":{
               "type":"stemmer",
               "language":"light_german"
            },
            "german_stop": {
               "type":       "stop",
               "stopwords":  "_german_"
            }
         },
         "analyzer":{
            "german":{
               "type":"standard",
               "stopwords": "_none_"
            },
            "german_stemmer":{
               "type":"custom",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_stemmer"
               ]
            },
            "german_normalization":{
               "type":"custom",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_normalization"
               ]
            },
            "german_dict_decompound":{
               "type":"custom",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_dict_decompounder",
                  "german_normalization",
                  "german_stemmer"
               ]
            },
            "german_decompound":{
               "type":"custom",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_decompounder",
                  "german_normalization",
                  "german_stemmer"
               ]
            },
            "german_stop": {
               "tokenizer": "standard",
               "filter": [
                   "lowercase",
                   "german_decompounder",
                   "german_stop",
                   "german_normalization",
                   "german_stemmer"
               ]
            }
         }
      }
   },
   "mappings":{
      "_doc":{
         "properties":{
            "product_name":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "short_description":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "long_description":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "search":{
               "type":"text",
               "analyzer":"german_decompound"
            }
         }
      }
   }
}
'

# Adding rows to the index
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/1" -H 'Content-Type: application/json' -d'
{
    "product_name" : "Passivhaus",
    "short_description" : "Haus mit U-Wert<10kWh/am2",
    "long_description" : "Haus mit U-Wert<10kWh/am2\r\n20m2 Solaranlage\r\n40m2 Photovoltaik\r\n7500l Regenwassertank, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/2" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Niedrigenergiehaus",
    "short_description" : "Haus mit U-Wert<45kWh/am2",
    "long_description" : "Haus mit U-Wert<45kWh/am2\r\n20 m2 Solaranlage, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/3" -H "Content-Type: application/json" -d '
{
    "product_name" : "Seegrundstück",
    "short_description" : "Seegrundstück am Attersee",
    "long_description" : "Seegrundstück am Attersee mit Seeblick und Bergblick, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/4" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Almgrundstück",
    "short_description" : "Almgrundstück an einem Bergsee",
    "long_description" : "Almgrundstück an einem Bergsee mit Zufahrtsstrasse, geschottert und Winterräumung, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/5" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Talgrundstück",
    "short_description" : "Grundstück am Talende",
    "long_description" : "Talgrundstück am Ende des Steyerlingtales, wenig Sonne, dafür viel kaltes Bachwasser direkt neben dem Grundstück, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder_stop/_doc/6" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Reihenhäuser",
    "short_description" : "Häuser mit Mehrwert",
    "long_description" : "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Counting the entries
curl -XGET 'http://localhost:9200/product_hyphen_decompounder_stop/_count?pretty=true'

# Displaying all entries
curl -XGET "http://localhost:9200/product_hyphen_decompounder_stop/_doc/_search?pretty=true" -H 'Content-Type: application/json' -d '
{
    "query" : {
        "match_all" : {}
    }
}
'
# Matching search terms in the copy_to field search
curl -X GET "localhost:9200/product_hyphen_decompounder_stop/_search" -H 'Content-Type: application/json' -d'
{
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'

# Using pagination for the matching results
curl -X GET "localhost:9200/product_hyphen_decompounder_stop/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 0, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'
curl -X GET "localhost:9200/product_hyphen_decompounder_stop/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 2, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'

# Testing different analyzers of the index

# If using only a text field ES uses the standard analyzer without custom filtering
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'
# Providing an analyzer forces ES to use the given one. In this case the standard analyzer.
# That gives the same result as above
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "standard",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Giving a field name forces ES to use the analyzer mapped to the field during index creation for the given text
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "product_name",
  "text": "Reihenhäuser"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Häuser mit Mehrwert"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "long_description",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "search",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using the analyzer german defined during index creation for a test text.
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using the filter german stemmer defined during index creation for a test text.
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_stemmer",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge erreichbar über eine Straße"
}
'

# Using the analyzer german defined during index creation for a test text.
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_normalization",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge erreichbar über eine Straße"
}
'

# Using the analyzer german_dict_decompound, that uses a german word decompounder against a dictionary (brute force)
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_dict_decompound",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'
# compare the hyphenation decompounder with the dictionary decompounder
# matches grund, because short_description has been asigned the hyphenation decompounder
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'
# matches grund and rund!! but it is obvious, that rund ist not part of Almgrundstueck
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_dict_decompound",
  "text": "Almgrundstueck an einem Bergsee"
}
'

# Using the analyzer german_dict_decompound, that uses a intelligent algorithm to decompound words
# before they are compared to a german dictionary
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_decompound",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using an filter for stop words
curl -X POST "localhost:9200/product_hyphen_decompounder_stop/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_stop",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

curl -X DELETE "localhost:9200/product_hyphen_decompounder_stop"
