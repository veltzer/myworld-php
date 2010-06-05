# variables

ORIG=$(shell find orig -type f)
ALL_TARGETS=
BIG_PNG=$(addprefix big/,$(addsuffix .png,$(basename $(notdir $(ORIG)))))
ALL_TARGETS+=$(BIG_PNG)
SMALL_PNG=$(addprefix small/,$(addsuffix .png,$(basename $(notdir $(ORIG)))))
ALL_TARGETS+=$(SMALL_PNG)
TARGETS=$(addprefix small/,$(notdir $(SOURCES)))

BIG_SIZE=256x128
SMALL_SIZE=64x32
OPTS=-background white -type TrueColorMatte -gravity center

# targets

.PHONY: all
all: $(ALL_TARGETS)

.PHONY: clean
clean:
	-rm -f $(ALL_TARGETS) small/* big/*

.PHONY: debug
debug:
	@echo $(ORIG)
	@echo $(BIG_PNG)
	@echo $(SMALL_PNG)

# rules

big/%.png: orig/%.gif
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
big/%.png: orig/%.jpg
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
big/%.png: orig/%.png
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
small/%.png: orig/%.gif
	convert -resize $(SMALL_SIZE) $(OPTS) -extent $(SMALL_SIZE) $^ $@
small/%.png: orig/%.jpg
	convert -resize $(SMALL_SIZE) $(OPTS) -extent $(SMALL_SIZE) $^ $@
small/%.png: orig/%.png
	convert -resize $(SMALL_SIZE) $(OPTS) -extent $(SMALL_SIZE) $^ $@
