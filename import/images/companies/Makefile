# variables

SRC_DIR:=src/
BIG_DIR:=target/big/
SMA_DIR:=target/sma/

FILES_SRC:=$(shell find $(SRC_DIR) -type f)
ALL_TARGETS:=
FILES_BIG_PNG:=$(addprefix $(BIG_DIR),$(addsuffix .png,$(basename $(notdir $(FILES_SRC)))))
ALL_TARGETS:=$(ALL_TARGETS) $(FILES_BIG_PNG)
FILES_SMA_PNG:=$(addprefix $(SMA_DIR),$(addsuffix .png,$(basename $(notdir $(FILES_SRC)))))
ALL_TARGETS:=$(ALL_TARGETS) $(FILES_SMA_PNG)

BIG_SIZE:=256x128
SMA_SIZE:=64x32
OPTS:=-background white -type TrueColorMatte -gravity center

# targets

.PHONY: all
all: $(ALL_TARGETS)

.PHONY: clean
clean:
	-rm -f $(ALL_TARGETS) $(BIG_DIR)* $(SMA_DIR)*

.PHONY: debug
debug:
	$(info FILES_SRC is $(FILES_SRC))
	$(info FILES_BIG_PNG $(FILES_BIG_PNG))
	$(info FILES_SMA_PNG $(FILES_SMA_PNG))

# rules

$(BIG_DIR)%.png: $(SRC_DIR)%.gif
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
$(BIG_DIR)%.png: $(SRC_DIR)%.jpg
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
$(BIG_DIR)%.png: $(SRC_DIR)%.png
	convert -resize $(BIG_SIZE) $(OPTS) -extent $(BIG_SIZE) $^ $@
$(SMA_DIR)%.png: $(SRC_DIR)%.gif
	convert -resize $(SMA_SIZE) $(OPTS) -extent $(SMA_SIZE) $^ $@
$(SMA_DIR)%.png: $(SRC_DIR)%.jpg
	convert -resize $(SMA_SIZE) $(OPTS) -extent $(SMA_SIZE) $^ $@
$(SMA_DIR)%.png: $(SRC_DIR)%.png
	convert -resize $(SMA_SIZE) $(OPTS) -extent $(SMA_SIZE) $^ $@
