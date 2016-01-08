/*
ChemicalTagger library from https://bitbucket.org/wwmm/chemicaltagger/ and adapted from the examples.
*/

import java.util.*;
import java.io.*;
import java.nio.file.*;
import java.nio.charset.*;

import uk.ac.cam.ch.wwmm.chemicaltagger.POSContainer;
import uk.ac.cam.ch.wwmm.chemicaltagger.ChemistryPOSTagger;
import uk.ac.cam.ch.wwmm.chemicaltagger.ChemistrySentenceParser;
import uk.ac.cam.ch.wwmm.chemicaltagger.Utils;
import nu.xom.*;

import org.antlr.v4.runtime.tree.ParseTree;
import org.antlr.v4.runtime.tree.Tree;

public class ChemTagger
{
	public static void main(String[] args)
	{
		new ChemTagger(args[0], args[1]);	
	}

	public ChemTagger(String path, String name)
	{
		//read string from file
		try{
			List<String> lines = Files.readAllLines(Paths.get(path), Charset.forName("UTF-8"));		
			for (String line : lines)
			{
				//print out any findings from each line
				POSContainer posContainer = ChemistryPOSTagger.getDefaultInstance().runTaggers(line);

				ChemistrySentenceParser chemistrySentenceParser = new ChemistrySentenceParser(posContainer);
			
				chemistrySentenceParser.parseTags();

				Document doc = chemistrySentenceParser.makeXMLDocument();

				//NodeList oscarCMs = doc.getElementsByTagName("OSCAR-CM");

				Element root = doc.getRootElement();
				listChildren(root, name);
				
			}					
		} catch (IOException e){
			e.printStackTrace();
		}
	}

	public static void listChildren(Node current, String name) {   
		String data = "";
		if (current instanceof Element) {
        		Element temp = (Element) current;			
        		if(temp.getQualifiedName().equals(name)){				
				Node value = temp.getChild(0);
				if(value instanceof Text)
				{
					System.out.println(value.getValue());
				}
			}
    		}
    		for (int i = 0; i < current.getChildCount(); i++) {
      			listChildren(current.getChild(i), name);
    		}
  	}
}
